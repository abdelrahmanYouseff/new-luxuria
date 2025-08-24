<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Services\CouponInvoiceService;
use App\Services\PointSysService;
use App\Services\ExternalBookingService;
use App\Services\BookingPointsService;
use App\Services\BookingInvoiceService;
use App\Mail\PaymentConfirmationMail;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripeController extends Controller
{
    private CouponInvoiceService $couponInvoiceService;
    private PointSysService $pointSysService;
    private ExternalBookingService $externalBookingService;
    private BookingPointsService $bookingPointsService;
    private BookingInvoiceService $bookingInvoiceService;

    public function __construct(
        CouponInvoiceService $couponInvoiceService,
        PointSysService $pointSysService,
        ExternalBookingService $externalBookingService,
        BookingPointsService $bookingPointsService,
        BookingInvoiceService $bookingInvoiceService
    ) {
        $this->couponInvoiceService = $couponInvoiceService;
        $this->pointSysService = $pointSysService;
        $this->externalBookingService = $externalBookingService;
        $this->bookingPointsService = $bookingPointsService;
        $this->bookingInvoiceService = $bookingInvoiceService;

        // Only set Stripe API key if it's a real key (not placeholder)
        $stripeKey = config('services.stripe.secret_key');
        if ($stripeKey && !str_contains($stripeKey, 'your_secret_key_here')) {
            Stripe::setApiKey($stripeKey);
        }
    }

    /**
     * Check if we should use mock mode
     */
    private function shouldUseMock(): bool
    {
        $stripeKey = config('services.stripe.secret_key');
        return !$stripeKey || str_contains($stripeKey, 'your_secret_key_here');
    }

    /**
     * Create mock payment intent for testing
     */
    private function createMockPaymentIntent(array $data): array
    {
        $mockPaymentIntentId = 'pi_mock_' . uniqid();

        Log::info('Creating mock payment intent for testing', [
            'payment_intent_id' => $mockPaymentIntentId,
            'amount' => $data['amount'],
            'coupon_id' => $data['coupon_id'],
            'user_id' => $data['user_id']
        ]);

        return [
            'id' => $mockPaymentIntentId,
            'client_secret' => $mockPaymentIntentId . '_secret_mock',
            'amount' => $data['amount'],
            'currency' => 'aed',
            'status' => 'requires_payment_method',
            'metadata' => [
                'coupon_id' => $data['coupon_id'],
                'coupon_name' => $data['coupon_name'],
                'user_id' => $data['user_id'],
            ]
        ];
    }

    /**
     * Create payment intent for coupon purchase
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated',
            ], 401);
        }

        $request->validate([
            'coupon_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'coupon_name' => 'required|string|max:255',
        ]);

        try {
            $amount = (int) ($request->amount * 100); // Convert to cents

            // Use mock payment intent if Stripe keys are not configured
            if ($this->shouldUseMock()) {
                $paymentIntent = (object) $this->createMockPaymentIntent([
                    'amount' => $amount,
                    'coupon_id' => $request->coupon_id,
                    'coupon_name' => $request->coupon_name,
                    'user_id' => $user->id,
                ]);
            } else {
                $paymentIntent = PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => 'aed',
                    'metadata' => [
                        'coupon_id' => $request->coupon_id,
                        'coupon_name' => $request->coupon_name,
                        'user_id' => $user->id,
                    ],
                    'description' => "Purchase: {$request->coupon_name}",
                ]);
            }

            // Create coupon invoice
            $invoice = $this->couponInvoiceService->createInvoice([
                'user_id' => $user->id,
                'coupon_id' => $request->coupon_id,
                'coupon_name' => $request->coupon_name,
                'amount' => $request->amount,
                'currency' => 'AED',
                'stripe_payment_intent_id' => $paymentIntent->id,
                'customer_email' => $user->email,
                'customer_name' => $user->name,
            ]);

                        Log::info('Stripe payment intent created with invoice', [
                'payment_intent_id' => $paymentIntent->id,
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => $amount,
                'coupon_id' => $request->coupon_id,
                'user_id' => $user->id,
            ]);

            // For mock mode, redirect to mock payment page
            if ($this->shouldUseMock()) {
                $mockPaymentUrl = route('mock.payment', [
                    'amount' => $request->amount,
                    'coupon_name' => $request->coupon_name,
                    'payment_intent_id' => $paymentIntent->id,
                    'coupon_id' => $request->coupon_id
                ]);

                return response()->json([
                    'success' => true,
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'invoice_number' => $invoice->invoice_number,
                    'mock_payment_url' => $mockPaymentUrl,
                    'is_mock' => true,
                ]);
            }

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'invoice_number' => $invoice->invoice_number,
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error: ' . $e->getMessage(), [
                'coupon_id' => $request->coupon_id,
                'amount' => $request->amount,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Payment processing failed',
                'message' => $e->getMessage(),
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error in payment processing: ' . $e->getMessage(), [
                'coupon_id' => $request->coupon_id,
                'amount' => $request->amount,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }

    /**
     * Handle successful payment
     */
    public function handlePaymentSuccess(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated',
            ], 401);
        }

        $request->validate([
            'payment_intent_id' => 'required|string',
            'coupon_id' => 'required|integer',
        ]);

        try {
            // Handle mock payment intents for testing
            if ($this->shouldUseMock() && str_contains($request->payment_intent_id, 'pi_mock_')) {
                // Create mock succeeded payment intent
                $paymentIntent = (object) [
                    'id' => $request->payment_intent_id,
                    'status' => 'succeeded',
                    'amount' => 50000, // Mock amount in cents
                    'currency' => 'aed',
                    'payment_method_types' => ['card'],
                    'latest_charge' => 'ch_mock_' . uniqid(),
                ];

                Log::info('Processing mock payment intent', [
                    'payment_intent_id' => $request->payment_intent_id,
                    'status' => 'succeeded'
                ]);
            } else {
                $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            }

            if ($paymentIntent->status === 'succeeded') {
                // Process successful payment and update invoice
                $invoice = $this->couponInvoiceService->processSuccessfulPayment(
                    $request->payment_intent_id,
                    [
                        'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                        'amount_paid' => $paymentIntent->amount / 100,
                        'currency' => $paymentIntent->currency,
                        'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
                    ]
                );

                if ($invoice) {
                    Log::info('Payment successful with invoice updated', [
                        'payment_intent_id' => $request->payment_intent_id,
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'coupon_id' => $request->coupon_id,
                        'user_id' => $user->id,
                        'amount' => $paymentIntent->amount / 100,
                    ]);

                    // Send confirmation email to user
                    try {
                        $couponDetails = [
                            'name' => $invoice->coupon_name,
                            'amount' => $invoice->amount,
                            'currency' => $invoice->currency,
                        ];

                        Mail::to($user->email)->send(new PaymentConfirmationMail($invoice, $couponDetails));
                        Log::info('Payment confirmation email sent successfully', [
                            'invoice_id' => $invoice->id,
                            'user_email' => $user->email,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send payment confirmation email', [
                            'invoice_id' => $invoice->id,
                            'user_email' => $user->email,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Add 500 points to user after successful coupon purchase
                    $pointsAdded = false;
                    if ($user->pointsys_customer_id) {
                        try {
                            $pointsResult = $this->pointSysService->addPointsToCustomerByUser(
                                $user,
                                500,
                                'نقاط شراء كوبون - ' . $invoice->coupon_name,
                                'COUPON_PURCHASE_' . $invoice->invoice_number
                            );

                            if ($pointsResult && isset($pointsResult['status']) && $pointsResult['status'] === 'success') {
                                $pointsAdded = true;
                                Log::info('Points added successfully after coupon purchase', [
                                    'user_id' => $user->id,
                                    'points_added' => 500,
                                    'invoice_number' => $invoice->invoice_number,
                                    'pointsys_response' => $pointsResult
                                ]);
                            } else {
                                Log::warning('Failed to add points after coupon purchase', [
                                    'user_id' => $user->id,
                                    'invoice_number' => $invoice->invoice_number,
                                    'pointsys_response' => $pointsResult
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Exception while adding points after coupon purchase', [
                                'user_id' => $user->id,
                                'invoice_number' => $invoice->invoice_number,
                                'error' => $e->getMessage()
                            ]);
                        }
                    } else {
                        Log::warning('User not registered in PointSys, cannot add points', [
                            'user_id' => $user->id,
                            'invoice_number' => $invoice->invoice_number
                        ]);
                    }

                    $message = 'Payment successful! Your coupon has been purchased.';
                    if ($pointsAdded) {
                        $message .= ' You have earned 500 bonus points!';
                    }

                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'payment_intent_id' => $request->payment_intent_id,
                        'invoice_number' => $invoice->invoice_number,
                        'points_added' => $pointsAdded ? 500 : 0,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Invoice not found',
                    ], 404);
                }
            } else {
                // Process failed payment
                $this->couponInvoiceService->processFailedPayment(
                    $request->payment_intent_id,
                    'Payment not completed: ' . $paymentIntent->status
                );

                return response()->json([
                    'success' => false,
                    'error' => 'Payment not completed',
                    'status' => $paymentIntent->status,
                ], 400);
            }

        } catch (ApiErrorException $e) {
            Log::error('Error retrieving payment intent: ' . $e->getMessage(), [
                'payment_intent_id' => $request->payment_intent_id,
                'coupon_id' => $request->coupon_id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Payment verification failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createCheckoutSession(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated',
            ], 401);
        }

        $request->validate([
            'coupon_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'coupon_name' => 'required|string|max:255',
            'payment_intent_id' => 'required|string',
        ]);

        try {
            $amount = (int) ($request->amount * 100); // Convert to cents

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'aed',
                        'product_data' => [
                            'name' => $request->coupon_name,
                            'description' => 'Coupon purchase from Luxuria UAE',
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('coupons.index'),
                'metadata' => [
                    'coupon_id' => $request->coupon_id,
                    'coupon_name' => $request->coupon_name,
                    'user_id' => $user->id,
                    'payment_intent_id' => $request->payment_intent_id,
                ],
            ]);

            Log::info('Stripe checkout session created', [
                'session_id' => $session->id,
                'amount' => $amount,
                'coupon_id' => $request->coupon_id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'url' => $session->url,
                'session_id' => $session->id,
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session error: ' . $e->getMessage(), [
                'coupon_id' => $request->coupon_id,
                'amount' => $request->amount,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Checkout session creation failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload in webhook: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature in webhook: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $invoice = $this->couponInvoiceService->processSuccessfulPayment(
                    $paymentIntent->id,
                    [
                        'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                        'amount_paid' => $paymentIntent->amount / 100,
                        'currency' => $paymentIntent->currency,
                        'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
                    ]
                );

                // Add 500 points to user after successful coupon purchase via webhook
                if ($invoice && $invoice->user) {
                    $user = $invoice->user;
                    if ($user->pointsys_customer_id) {
                        try {
                            $pointsResult = $this->pointSysService->addPointsToCustomerByUser(
                                $user,
                                500,
                                'نقاط شراء كوبون - ' . $invoice->coupon_name,
                                'COUPON_PURCHASE_WEBHOOK_' . $invoice->invoice_number
                            );

                            if ($pointsResult && isset($pointsResult['status']) && $pointsResult['status'] === 'success') {
                                Log::info('Points added successfully after coupon purchase via webhook', [
                                    'user_id' => $user->id,
                                    'points_added' => 500,
                                    'invoice_number' => $invoice->invoice_number,
                                    'payment_intent_id' => $paymentIntent->id
                                ]);
                            } else {
                                Log::warning('Failed to add points after coupon purchase via webhook', [
                                    'user_id' => $user->id,
                                    'invoice_number' => $invoice->invoice_number,
                                    'payment_intent_id' => $paymentIntent->id
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Exception while adding points after coupon purchase via webhook', [
                                'user_id' => $user->id,
                                'invoice_number' => $invoice->invoice_number,
                                'payment_intent_id' => $paymentIntent->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->couponInvoiceService->processFailedPayment(
                    $paymentIntent->id,
                    'Payment failed: ' . ($paymentIntent->last_payment_error->message ?? 'Unknown error')
                );
                break;

            case 'checkout.session.completed':
                $session = $event->data->object;

                // Handle booking payment completion
                if (isset($session->metadata->booking_id)) {
                    $bookingId = $session->metadata->booking_id;
                    $booking = Booking::with('vehicle')->find($bookingId);

                                        if ($booking && $session->payment_status === 'paid') {
                        // Mark booking as confirmed
                        $booking->update([
                            'status' => 'confirmed',
                            'stripe_session_id' => $session->id,
                        ]);

                        // Update external booking status if exists
                        if ($booking->external_reservation_id) {
                            $externalUpdateResult = $this->externalBookingService->updateExternalBookingStatus(
                                $booking->external_reservation_id,
                                'confirmed'
                            );

                            if ($externalUpdateResult['success']) {
                                Log::info('External booking status updated to confirmed via webhook', [
                                    'booking_id' => $booking->id,
                                    'external_booking_id' => $booking->external_reservation_id,
                                ]);
                            } else {
                                Log::warning('Failed to update external booking status via webhook', [
                                    'booking_id' => $booking->id,
                                    'external_booking_id' => $booking->external_reservation_id,
                                    'error' => $externalUpdateResult['message'],
                                ]);
                            }
                        }

                        Log::info('Booking confirmed via webhook', [
                            'booking_id' => $booking->id,
                            'session_id' => $session->id,
                            'amount' => $booking->total_amount,
                        ]);
                    }
                }

                // Handle coupon payment completion (existing logic)
                Log::info('Checkout session completed', ['session_id' => $session->id]);
                break;

            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Get user's invoices
     */
    public function getUserInvoices(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $invoices = $this->couponInvoiceService->getUserInvoices($user, 20);

        return response()->json([
            'success' => true,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Get Stripe public key for frontend
     */
    public function getPublicKey(): JsonResponse
    {
        return response()->json([
            'public_key' => config('services.stripe.public_key'),
        ]);
    }

    /**
     * Handle Stripe success page and add points
     */
    public function handleSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            Log::warning('Success page accessed without session_id');
            return view('stripe.success', [
                'success' => false,
                'message' => 'Session ID not found',
                'points_added' => 0,
            ]);
        }

        try {
            // Retrieve Stripe session
            $session = Session::retrieve($sessionId);

            Log::info('Stripe success page accessed', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status,
                'customer_email' => $session->customer_email,
            ]);

                        if ($session->payment_status === 'paid') {
                // Get original payment intent ID from session metadata
                $originalPaymentIntentId = $session->metadata->payment_intent_id ?? null;

                Log::info('Looking for invoice with original payment intent ID', [
                    'original_payment_intent_id' => $originalPaymentIntentId,
                    'session_payment_intent_id' => $session->payment_intent,
                    'session_id' => $sessionId,
                ]);

                // Find invoice by original payment intent ID from metadata
                $invoice = null;
                if ($originalPaymentIntentId) {
                    $invoice = $this->couponInvoiceService->getInvoiceByPaymentIntent($originalPaymentIntentId);
                }

                // If not found with original payment intent, try with session payment intent
                if (!$invoice) {
                    $invoice = $this->couponInvoiceService->getInvoiceByPaymentIntent($session->payment_intent);
                }

                // If still not found, try to find by user and coupon metadata
                if (!$invoice && isset($session->metadata->user_id) && isset($session->metadata->coupon_id)) {
                    $invoice = \App\Models\CouponInvoice::where('user_id', $session->metadata->user_id)
                        ->where('coupon_id', $session->metadata->coupon_id)
                        ->whereIn('payment_status', ['pending', 'completed'])
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($invoice) {
                        Log::info('Found invoice by user and coupon metadata', [
                            'invoice_id' => $invoice->id,
                            'invoice_number' => $invoice->invoice_number,
                            'user_id' => $session->metadata->user_id,
                            'coupon_id' => $session->metadata->coupon_id,
                        ]);
                    }
                }

                if (!$invoice) {
                    Log::warning('Invoice not found with any method', [
                        'original_payment_intent_id' => $originalPaymentIntentId,
                        'session_payment_intent_id' => $session->payment_intent,
                        'session_id' => $sessionId,
                        'metadata' => $session->metadata,
                    ]);

                    return view('stripe.success', [
                        'success' => false,
                        'message' => 'Invoice not found',
                        'points_added' => 0,
                    ]);
                }

                // Mark invoice as paid if not already
                if ($invoice->payment_status !== 'completed') {
                    $invoice->markAsPaid([
                        'payment_method' => 'stripe_checkout',
                        'amount_paid' => $session->amount_total / 100,
                        'currency' => $session->currency,
                        'stripe_session_id' => $sessionId,
                        'stripe_final_payment_intent_id' => $session->payment_intent,
                        'completed_at' => now(),
                    ]);

                    Log::info('Invoice marked as paid from success page', [
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'session_id' => $sessionId,
                        'original_payment_intent' => $originalPaymentIntentId,
                        'final_payment_intent' => $session->payment_intent,
                    ]);
                }

                // 💰 Add 500 points to user after successful coupon purchase
                $pointsAdded = false;
                $user = $invoice->user;

                if ($user && $user->pointsys_customer_id) {
                    try {
                        // Check if points were already added for this invoice
                        $existingPointsLog = storage_path('logs/laravel.log');
                        $referenceId = 'COUPON_PURCHASE_SUCCESS_' . $invoice->invoice_number;

                        // Simple check to avoid duplicate points
                        if (file_exists($existingPointsLog)) {
                            $logContent = file_get_contents($existingPointsLog);
                            if (strpos($logContent, $referenceId) !== false) {
                                Log::info('Points already added for this invoice', [
                                    'invoice_number' => $invoice->invoice_number,
                                    'reference_id' => $referenceId,
                                ]);
                                $pointsAdded = true; // Consider points as added
                            }
                        }

                        if (!$pointsAdded) {
                            $pointsResult = $this->pointSysService->addPointsToCustomerByUser(
                                $user,
                                500,
                                'نقاط شراء كوبون - ' . $invoice->coupon_name,
                                $referenceId
                            );

                            if ($pointsResult && isset($pointsResult['status']) && $pointsResult['status'] === 'success') {
                                $pointsAdded = true;
                                Log::info('Points added successfully from success page', [
                                    'user_id' => $user->id,
                                    'points_added' => 500,
                                    'invoice_number' => $invoice->invoice_number,
                                    'session_id' => $sessionId,
                                    'pointsys_response' => $pointsResult
                                ]);
                            } else {
                                Log::warning('Failed to add points from success page', [
                                    'user_id' => $user->id,
                                    'invoice_number' => $invoice->invoice_number,
                                    'session_id' => $sessionId,
                                    'pointsys_response' => $pointsResult
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Exception while adding points from success page', [
                            'user_id' => $user->id,
                            'invoice_number' => $invoice->invoice_number,
                            'session_id' => $sessionId,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    Log::warning('User not found or not registered in PointSys', [
                        'invoice_id' => $invoice->id,
                        'user_id' => $invoice->user_id,
                        'session_id' => $sessionId,
                    ]);
                }

                $successMessage = 'Payment completed successfully! Your coupon has been purchased.';
                if ($pointsAdded) {
                    $successMessage .= ' You have earned 500 bonus points!';
                }

                return view('stripe.success', [
                    'success' => true,
                    'message' => $successMessage,
                    'points_added' => $pointsAdded ? 500 : 0,
                    'invoice_number' => $invoice->invoice_number,
                    'coupon_name' => $invoice->coupon_name,
                    'amount' => $invoice->amount,
                    'user_name' => $user ? $user->name : 'Unknown',
                ]);

            } else {
                Log::warning('Payment not completed in session', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status,
                ]);

                return view('stripe.success', [
                    'success' => false,
                    'message' => 'Payment was not completed successfully',
                    'points_added' => 0,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in Stripe success page', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('stripe.success', [
                'success' => false,
                'message' => 'An error occurred while processing your payment',
                'points_added' => 0,
            ]);
        }
    }

    /**
     * Create payment session for booking
     */
    public function createBookingPaymentSession(Request $request)
    {
        try {
            $bookingId = $request->input('booking_id');
            $booking = Booking::with(['vehicle', 'user'])->findOrFail($bookingId);

            // Verify the booking belongs to the authenticated user
            if ($booking->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to booking.'
                ], 403);
            }

            // Check if we should use mock mode
            if ($this->shouldUseMock()) {
                // Return mock payment URL for testing
                return response()->json([
                    'success' => true,
                    'payment_url' => route('booking.mock.payment', ['booking' => $booking->id]),
                    'mock_mode' => true
                ]);
            }

            // Create real Stripe checkout session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'aed',
                        'product_data' => [
                            'name' => 'Vehicle Rental - ' . $booking->vehicle->make . ' ' . $booking->vehicle->model,
            'description' => 'Rental period: ' . optional($booking->start_date)->format('d/m/Y') . ' to ' . optional($booking->end_date)->format('d/m/Y') . ' (' . $booking->total_days . ' days)',
                        ],
                        'unit_amount' => $booking->total_amount * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('booking.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cars.show', ['id' => $booking->vehicle_id]),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'vehicle_id' => $booking->vehicle_id,
                ],
            ]);

            Log::info('Created Stripe checkout session for booking', [
                'booking_id' => $booking->id,
                'session_id' => $session->id,
                'amount' => $booking->total_amount,
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $session->url,
                'session_id' => $session->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating booking payment session', [
                'booking_id' => $request->input('booking_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to create payment session. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle successful booking payment
     */
    public function handleBookingPaymentSuccess(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');

            if (!$sessionId) {
                return redirect()->route('home')->with('booking_error', 'Invalid payment session');
            }

            if ($this->shouldUseMock()) {
                // Handle mock payment success
                $bookingId = $request->query('booking_id');
                $booking = Booking::findOrFail($bookingId);

                // Mark booking as confirmed
                $booking->update(['status' => 'confirmed']);

                // Create booking invoice
                $bookingInvoice = $this->bookingInvoiceService->createInvoice($booking);

                // Add points to customer
                $pointsResult = $this->bookingPointsService->addPointsToCustomer($booking);

                Log::info('Mock booking payment completed', [
                    'booking_id' => $booking->id,
                    'session_id' => $sessionId,
                    'points_result' => $pointsResult,
                ]);

                // Send confirmation email to user (for mock payments too)
                try {
                    Mail::to($booking->user->email)->send(new BookingConfirmationMail($booking));
                    Log::info('Mock booking confirmation email sent successfully', [
                        'booking_id' => $booking->id,
                        'user_email' => $booking->user->email,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send mock booking confirmation email', [
                        'booking_id' => $booking->id,
                        'user_email' => $booking->user->email,
                        'error' => $e->getMessage(),
                    ]);
                }

                return redirect()->route('home')->with('booking_success', [
                    'vehicle_name' => $booking->vehicle->make . ' ' . $booking->vehicle->model,
                    'booking_id' => $booking->id,
                    'start_date' => optional($booking->start_date)->format('d/m/Y'),
                    'end_date' => optional($booking->end_date)->format('d/m/Y'),
                    'external_reservation_uid' => $booking->external_reservation_uid,
                ]);
            }

            // Handle real Stripe payment
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $bookingId = $session->metadata->booking_id;
                $booking = Booking::with('vehicle')->findOrFail($bookingId);

                // Mark booking as confirmed
                $booking->update([
                    'status' => 'confirmed',
                    'stripe_session_id' => $sessionId,
                ]);

                // Update local vehicle status to Rented
                try {
                    if ($booking->vehicle) {
                        $booking->vehicle->update(['status' => 'Rented']);
                        Log::info('Vehicle status updated to Rented after payment', [
                            'vehicle_id' => $booking->vehicle->id,
                            'booking_id' => $booking->id,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to update vehicle status after payment', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Update external booking status if exists (prefer UID if available)
                if ($booking->external_reservation_uid || $booking->external_reservation_id) {
                    $externalUpdateResult = null;
                    if ($booking->external_reservation_uid) {
                        $externalUpdateResult = $this->externalBookingService->updateExternalBookingStatus(
                            $booking->external_reservation_uid,
                            'confirmed',
                            true
                        );
                    } else {
                        $externalUpdateResult = $this->externalBookingService->updateExternalBookingStatus(
                            $booking->external_reservation_id,
                            'confirmed',
                            false
                        );
                    }

                    if ($externalUpdateResult['success']) {
                        Log::info('External booking status updated to confirmed', [
                            'booking_id' => $booking->id,
                            'external_booking_id' => $booking->external_reservation_id,
                        ]);
                    } else {
                        Log::warning('Failed to update external booking status', [
                            'booking_id' => $booking->id,
                            'external_booking_id' => $booking->external_reservation_id,
                            'error' => $externalUpdateResult['message'],
                        ]);
                    }
                } else {
                    // Try to retrieve cached UID (fallback) if not stored on booking
                    try {
                        $cachedUid = \Cache::get('booking_uid_by_user_' . (Auth::id() ?? 'guest'))
                            ?? \Cache::get('booking_uid_by_session_' . session()->getId());
                        if ($cachedUid) {
                            $externalUpdateResult = $this->externalBookingService->updateExternalBookingStatus(
                                $cachedUid,
                                'confirmed',
                                true
                            );
                            if ($externalUpdateResult && ($externalUpdateResult['success'] ?? false)) {
                                \Log::info('External booking status updated via cached UID', [
                                    'booking_id' => $booking->id,
                                    'cached_uid' => $cachedUid,
                                ]);
                            }
                        }
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to update external booking via cached UID', [
                            'booking_id' => $booking->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Create booking invoice
                $bookingInvoice = $this->bookingInvoiceService->createInvoice($booking);

                // Add points to customer
                $pointsResult = $this->bookingPointsService->addPointsToCustomer($booking);

                Log::info('Booking payment completed successfully', [
                    'booking_id' => $booking->id,
                    'session_id' => $sessionId,
                    'amount' => $booking->total_amount,
                    'points_result' => $pointsResult,
                    'invoice_id' => $bookingInvoice->id,
                ]);

                // Send confirmation email to user
                try {
                    Mail::to($booking->user->email)->send(new BookingConfirmationMail($booking));
                    Log::info('Booking confirmation email sent successfully', [
                        'booking_id' => $booking->id,
                        'user_email' => $booking->user->email,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send booking confirmation email', [
                        'booking_id' => $booking->id,
                        'user_email' => $booking->user->email,
                        'error' => $e->getMessage(),
                    ]);
                }

                return redirect()->route('home')->with('booking_success', [
                    'vehicle_name' => $booking->vehicle->make . ' ' . $booking->vehicle->model,
                    'booking_id' => $booking->id,
                    'start_date' => optional($booking->start_date)->format('d/m/Y'),
                    'end_date' => optional($booking->end_date)->format('d/m/Y'),
                    'external_reservation_uid' => $booking->external_reservation_uid,
                ]);

            } else {
                Log::warning('Booking payment not completed', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status,
                ]);

                return redirect()->route('home')->with('booking_error', 'Payment was not completed successfully');
            }

        } catch (\Exception $e) {
            Log::error('Error handling booking payment success', [
                'session_id' => $request->query('session_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('home')->with('booking_error', 'An error occurred while processing your payment');
        }
    }
}
