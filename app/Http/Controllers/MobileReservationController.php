<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\User;
use App\Services\ExternalBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class MobileReservationController extends Controller
{
    protected $externalBookingService;

    public function __construct(ExternalBookingService $externalBookingService)
    {
        $this->externalBookingService = $externalBookingService;
    }

    /**
     * Create automatic pending reservation in RLAPP
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createReservation(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'vehicle_id' => 'required|exists:vehicles,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'emirate' => 'required|string|in:Dubai,Abu Dhabi,Sharjah,Ajman,Umm Al Quwain,Ras Al Khaimah,Fujairah',
                'user_email' => 'required|email',
                'notes' => 'nullable|string|max:500',
                'pickup_location' => 'nullable|string|max:255',
                'dropoff_location' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get authenticated user
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }

            // Verify that the provided email matches the authenticated user's email
            if ($request->user_email !== $user->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'البريد الإلكتروني المرسل لا يطابق المستخدم المسجل الدخول'
                ], 403);
            }

            // Get vehicle
            $vehicle = Vehicle::findOrFail($request->vehicle_id);

            // Check vehicle availability
            if (strtolower($vehicle->status) !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'المركبة غير متاحة للحجز حالياً',
                    'vehicle_status' => $vehicle->status
                ], 400);
            }

            // Check for booking conflicts
            if (Booking::hasConflict($request->vehicle_id, $request->start_date, $request->end_date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'المركبة محجوزة في التواريخ المحددة'
                ], 400);
            }

            // Calculate pricing
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $totalDays = $startDate->diffInDays($endDate);

            if ($totalDays === 0) {
                $totalDays = 1; // Minimum 1 day
            }

            // Determine pricing type and rate
            $pricingType = 'daily';
            $appliedRate = (float) $vehicle->daily_rate;

            if ($totalDays >= 7 && $totalDays < 28 && (float) $vehicle->weekly_rate > 0) {
                $pricingType = 'weekly';
                $appliedRate = (float) $vehicle->weekly_rate / 7;
            } elseif ($totalDays >= 28 && (float) $vehicle->monthly_rate > 0) {
                $pricingType = 'monthly';
                $appliedRate = round(((float) $vehicle->monthly_rate) / 30, 2);
            }

            if ($appliedRate <= 0) {
                $appliedRate = max((float) $vehicle->daily_rate, 1);
            }

            $totalAmount = round($totalDays * $appliedRate, 2);

            // Prepare booking data for RLAPP
            $bookingData = [
                'emirate' => $request->emirate,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'daily_rate' => $vehicle->daily_rate,
                'pricing_type' => $pricingType,
                'applied_rate' => $appliedRate,
                'total_days' => $totalDays,
                'total_amount' => $totalAmount,
                'notes' => $request->notes ?? '',
                'pickup_location' => $request->pickup_location ?? $request->emirate,
                'dropoff_location' => $request->dropoff_location ?? $request->emirate
            ];

            Log::info('Mobile app creating reservation in RLAPP', [
                'user_id' => $user->id,
                'user_email' => $request->user_email,
                'vehicle_id' => $vehicle->id,
                'booking_data' => $bookingData
            ]);

            // Create reservation in RLAPP first
            $rlappResult = $this->externalBookingService->createExternalBooking(
                $bookingData,
                $user->id,
                $vehicle->id
            );

            if (!$rlappResult['success']) {
                Log::error('Failed to create reservation in RLAPP', [
                    'user_id' => $user->id,
                    'vehicle_id' => $vehicle->id,
                    'error' => $rlappResult['message']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'فشل في إنشاء الحجز في النظام الخارجي',
                    'external_error' => $rlappResult['message']
                ], 500);
            }

            // Create local booking record
            $localBooking = Booking::create([
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'emirate' => $request->emirate,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'daily_rate' => $vehicle->daily_rate,
                'pricing_type' => $pricingType,
                'applied_rate' => $appliedRate,
                'total_days' => $totalDays,
                'total_amount' => $totalAmount,
                'notes' => $request->notes ?? '',
                'status' => 'pending',
                'external_reservation_id' => $rlappResult['external_booking_id'] ?? null,
                'external_reservation_uid' => $rlappResult['external_booking_uid'] ?? null,
            ]);

            Log::info('Mobile reservation created successfully', [
                'local_booking_id' => $localBooking->id,
                'external_reservation_id' => $localBooking->external_reservation_id,
                'external_reservation_uid' => $localBooking->external_reservation_uid
            ]);

            // Return success response with booking details
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحجز بنجاح',
                'data' => [
                    'reservation' => [
                        'id' => $localBooking->id,
                        'external_reservation_id' => $localBooking->external_reservation_id,
                        'external_reservation_uid' => $localBooking->external_reservation_uid,
                        'status' => $localBooking->status,
                        'user_email' => $request->user_email,
                        'vehicle' => [
                            'id' => $vehicle->id,
                            'make' => $vehicle->make,
                            'model' => $vehicle->model,
                            'year' => $vehicle->year,
                            'plate_number' => $vehicle->plate_number
                        ],
                        'dates' => [
                            'start_date' => $localBooking->start_date,
                            'end_date' => $localBooking->end_date,
                            'total_days' => $localBooking->total_days
                        ],
                        'pricing' => [
                            'pricing_type' => $localBooking->pricing_type,
                            'applied_rate' => $localBooking->applied_rate,
                            'total_amount' => $localBooking->total_amount
                        ],
                        'location' => [
                            'emirate' => $localBooking->emirate,
                            'pickup_location' => $request->pickup_location ?? $request->emirate,
                            'dropoff_location' => $request->dropoff_location ?? $request->emirate
                        ],
                        'notes' => $localBooking->notes,
                        'created_at' => $localBooking->created_at->toISOString()
                    ],
                    'rlapp_response' => $rlappResult
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Mobile reservation creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الحجز',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's reservations for mobile app
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserReservations(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }

            $reservations = Booking::with('vehicle')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'external_reservation_id' => $booking->external_reservation_id,
                        'external_reservation_uid' => $booking->external_reservation_uid,
                        'status' => $booking->status,
                        'vehicle' => [
                            'id' => $booking->vehicle->id,
                            'make' => $booking->vehicle->make,
                            'model' => $booking->vehicle->model,
                            'year' => $booking->vehicle->year,
                            'plate_number' => $booking->vehicle->plate_number,
                            'image_url' => $booking->vehicle->image_url
                        ],
                        'dates' => [
                            'start_date' => $booking->start_date,
                            'end_date' => $booking->end_date,
                            'total_days' => $booking->total_days
                        ],
                        'pricing' => [
                            'pricing_type' => $booking->pricing_type,
                            'applied_rate' => $booking->applied_rate,
                            'total_amount' => $booking->total_amount
                        ],
                        'location' => [
                            'emirate' => $booking->emirate
                        ],
                        'notes' => $booking->notes,
                        'created_at' => $booking->created_at->toISOString()
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'تم جلب الحجوزات بنجاح',
                'data' => [
                    'reservations' => $reservations,
                    'total_count' => $reservations->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get user reservations', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب الحجوزات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a reservation
     *
     * @param Request $request
     * @param int $reservationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelReservation(Request $request, $reservationId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }

            $booking = Booking::where('id', $reservationId)
                ->where('user_id', $user->id)
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحجز غير موجود'
                ], 404);
            }

            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'الحجز ملغي مسبقاً'
                ], 400);
            }

            // Update status in RLAPP if external reservation exists
            if ($booking->external_reservation_id || $booking->external_reservation_uid) {
                $identifier = $booking->external_reservation_uid ?? $booking->external_reservation_id;
                $isUid = !empty($booking->external_reservation_uid);

                $rlappResult = $this->externalBookingService->updateExternalBookingStatus(
                    $identifier,
                    'cancelled',
                    $isUid
                );

                Log::info('RLAPP cancellation result', [
                    'booking_id' => $booking->id,
                    'external_id' => $identifier,
                    'rlapp_result' => $rlappResult
                ]);
            }

            // Update local booking status
            $booking->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الحجز بنجاح',
                'data' => [
                    'reservation_id' => $booking->id,
                    'status' => $booking->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cancel reservation', [
                'user_id' => Auth::id(),
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في إلغاء الحجز',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create Stripe checkout session for mobile reservation payment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'reservation_id' => 'required|exists:bookings,id',
                'success_url' => 'nullable|url',
                'cancel_url' => 'nullable|url'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get authenticated user
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }

            // Get the reservation
            $reservation = Booking::with('vehicle')->where('id', $request->reservation_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحجز غير موجود أو لا يخصك'
                ], 404);
            }

            // Check if reservation is already paid
            if ($reservation->status === 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'الحجز مؤكد ومدفوع مسبقاً'
                ], 400);
            }

            if ($reservation->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'الحجز ملغي ولا يمكن الدفع له'
                ], 400);
            }

            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret_key'));

            // Default URLs if not provided
            $successUrl = $request->success_url ?? 'https://wpp.rentluxuria.com/booking/payment/success';
            $cancelUrl = $request->cancel_url ?? 'https://wpp.rentluxuria.com/booking/payment/cancel';

            // Prepare line items for Stripe
            $lineItems = [
                [
                    'price_data' => [
                        'currency' => 'aed',
                        'product_data' => [
                            'name' => $reservation->vehicle->make . ' ' . $reservation->vehicle->model . ' (' . $reservation->vehicle->year . ')',
                            'description' => 'إيجار سيارة من ' . $reservation->start_date . ' إلى ' . $reservation->end_date . ' (' . $reservation->total_days . ' أيام)',
                            'images' => [$reservation->vehicle->image_url ?? 'https://wpp.rentluxuria.com/asset/image.png'],
                            'metadata' => [
                                'vehicle_plate' => $reservation->vehicle->plate_number,
                                'reservation_id' => $reservation->id,
                                'emirate' => $reservation->emirate
                            ]
                        ],
                        'unit_amount' => (int) ($reservation->total_amount * 100), // Convert to fils (cents)
                    ],
                    'quantity' => 1,
                ]
            ];

            // Create Stripe checkout session
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}&reservation_id=' . $reservation->id,
                'cancel_url' => $cancelUrl . '?reservation_id=' . $reservation->id,
                'customer_email' => $user->email,
                'client_reference_id' => $reservation->id,
                'metadata' => [
                    'reservation_id' => $reservation->id,
                    'user_id' => $user->id,
                    'vehicle_id' => $reservation->vehicle_id,
                    'external_reservation_id' => $reservation->external_reservation_id ?? '',
                    'external_reservation_uid' => $reservation->external_reservation_uid ?? '',
                    'start_date' => $reservation->start_date,
                    'end_date' => $reservation->end_date,
                    'total_amount' => $reservation->total_amount,
                    'emirate' => $reservation->emirate
                ],
                'expires_at' => time() + (30 * 60), // Session expires in 30 minutes
                'billing_address_collection' => 'auto',
                'shipping_address_collection' => [
                    'allowed_countries' => ['AE']
                ]
            ]);

            Log::info('Stripe checkout session created for mobile reservation', [
                'reservation_id' => $reservation->id,
                'user_id' => $user->id,
                'session_id' => $session->id,
                'checkout_url' => $session->url,
                'amount' => $reservation->total_amount
            ]);

            // Return the checkout URL
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء رابط الدفع بنجاح',
                'data' => [
                    'checkout_url' => $session->url,
                    'session_id' => $session->id,
                    'expires_at' => date('Y-m-d H:i:s', $session->expires_at),
                    'reservation' => [
                        'id' => $reservation->id,
                        'vehicle' => $reservation->vehicle->make . ' ' . $reservation->vehicle->model,
                        'total_amount' => $reservation->total_amount,
                        'currency' => 'AED',
                        'dates' => [
                            'start_date' => $reservation->start_date,
                            'end_date' => $reservation->end_date,
                            'total_days' => $reservation->total_days
                        ]
                    ]
                ]
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API error creating checkout session', [
                'user_id' => Auth::id(),
                'reservation_id' => $request->reservation_id,
                'error' => $e->getMessage(),
                'stripe_error_type' => $e->getError()->type ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطأ في نظام الدفع',
                'error' => 'فشل في إنشاء جلسة الدفع. يرجى المحاولة مرة أخرى.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to create checkout session for mobile reservation', [
                'user_id' => Auth::id(),
                'reservation_id' => $request->reservation_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في إنشاء رابط الدفع',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create quick checkout session (create reservation + payment in one step)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createQuickCheckout(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'vehicle_id' => 'required|exists:vehicles,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'emirate' => 'required|string|in:Dubai,Abu Dhabi,Sharjah,Ajman,Umm Al Quwain,Ras Al Khaimah,Fujairah',
                'user_email' => 'required|email',
                'notes' => 'nullable|string|max:500',
                'pickup_location' => 'nullable|string|max:255',
                'dropoff_location' => 'nullable|string|max:255',
                'success_url' => 'nullable|url',
                'cancel_url' => 'nullable|url'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // First create the reservation
            $reservationRequest = new Request($request->only([
                'vehicle_id', 'start_date', 'end_date', 'emirate', 'user_email',
                'notes', 'pickup_location', 'dropoff_location'
            ]));

            $reservationResponse = $this->createReservation($reservationRequest);
            $reservationData = $reservationResponse->getData(true);

            if (!$reservationData['success']) {
                return $reservationResponse;
            }

            $reservationId = $reservationData['data']['reservation']['id'];

            // Create checkout session for the new reservation
            $checkoutRequest = new Request([
                'reservation_id' => $reservationId,
                'success_url' => $request->success_url,
                'cancel_url' => $request->cancel_url
            ]);

            $checkoutResponse = $this->createCheckoutSession($checkoutRequest);
            $checkoutData = $checkoutResponse->getData(true);

            if (!$checkoutData['success']) {
                // If checkout fails, we might want to cancel the reservation
                Log::warning('Checkout failed after reservation creation', [
                    'reservation_id' => $reservationId,
                    'checkout_error' => $checkoutData['message']
                ]);

                return $checkoutResponse;
            }

            // Return combined response
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحجز ورابط الدفع بنجاح',
                'data' => [
                    'reservation' => $reservationData['data']['reservation'],
                    'checkout' => $checkoutData['data']
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create quick checkout', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في إنشاء الحجز والدفع',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
