<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Http\Controllers\StripeController;
use Illuminate\Http\Request;

class TestWebhook extends Command
{
    protected $signature = 'test:webhook {--booking-id=} {--event-type=checkout.session.completed}';
    protected $description = 'Test Stripe webhook locally';

    public function handle()
    {
        $this->info('🧪 Testing Stripe Webhook...');

        $bookingId = $this->option('booking-id');
        $eventType = $this->option('event-type');

        if (!$bookingId) {
            $booking = Booking::where('status', 'confirmed')->first();
            if (!$booking) {
                $this->error('No confirmed booking found. Please provide --booking-id');
                return 1;
            }
            $bookingId = $booking->id;
        }

        $booking = Booking::with(['user', 'vehicle'])->find($bookingId);
        if (!$booking) {
            $this->error("Booking with ID {$bookingId} not found.");
            return 1;
        }

        $this->info("📅 Testing with booking #{$booking->id}");
        $this->info("👤 User: {$booking->user->name} ({$booking->user->email})");
        $this->info("🚗 Vehicle: {$booking->vehicle->make} {$booking->vehicle->model}");
        $this->info("💰 Amount: {$booking->total_amount} AED");
        $this->info("📊 Points to earn: {$booking->points_earned}");
        $this->info("🔗 PointSys ID: {$booking->user->pointsys_customer_id}");

        // Create mock webhook event
        $mockEvent = $this->createMockWebhookEvent($booking, $eventType);

        // Create mock request with signature
        $payload = json_encode($mockEvent);
        $timestamp = time();
        $signature = 't=' . $timestamp . ',v1=' . hash_hmac('sha256', $timestamp . '.' . $payload, 'whsec_test');

        $request = Request::create('/stripe/webhook', 'POST', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => $signature
        ], $payload);

        // Test webhook handler
        $stripeController = app(StripeController::class);

        try {
            $response = $stripeController->handleWebhook($request);

            $this->info("✅ Webhook test completed!");
            $this->info("📊 Response status: " . $response->getStatusCode());

            // Check if booking was updated
            $booking->refresh();
            $this->info("📈 Booking status after webhook: {$booking->status}");
            $this->info("💳 Points added: " . ($booking->points_added_to_customer ? 'Yes' : 'No'));

        } catch (\Exception $e) {
            $this->error("❌ Webhook test failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function createMockWebhookEvent(Booking $booking, string $eventType): array
    {
        switch ($eventType) {
            case 'checkout.session.completed':
                return [
                    'id' => 'evt_mock_' . uniqid(),
                    'object' => 'event',
                    'api_version' => '2020-08-27',
                    'created' => time(),
                    'data' => [
                        'object' => [
                            'id' => 'cs_mock_' . uniqid(),
                            'object' => 'checkout.session',
                            'amount_total' => (int) ($booking->total_amount * 100),
                            'currency' => 'aed',
                            'customer_email' => $booking->user->email,
                            'payment_status' => 'paid',
                            'status' => 'complete',
                            'metadata' => [
                                'booking_id' => $booking->id,
                                'reservation_id' => $booking->id,
                                'user_id' => $booking->user->id,
                                'vehicle_id' => $booking->vehicle_id,
                                'external_reservation_id' => $booking->external_reservation_id ?? '',
                                'external_reservation_uid' => $booking->external_reservation_uid ?? '',
                                'start_date' => $booking->start_date,
                                'end_date' => $booking->end_date,
                                'total_amount' => $booking->total_amount,
                                'emirate' => $booking->emirate
                            ]
                        ]
                    ],
                    'livemode' => false,
                    'pending_webhooks' => 1,
                    'request' => [
                        'id' => 'req_mock_' . uniqid(),
                        'idempotency_key' => null
                    ],
                    'type' => $eventType
                ];

            case 'payment_intent.succeeded':
                return [
                    'id' => 'evt_mock_' . uniqid(),
                    'object' => 'event',
                    'api_version' => '2020-08-27',
                    'created' => time(),
                    'data' => [
                        'object' => [
                            'id' => 'pi_mock_' . uniqid(),
                            'object' => 'payment_intent',
                            'amount' => (int) ($booking->total_amount * 100),
                            'currency' => 'aed',
                            'status' => 'succeeded',
                            'metadata' => [
                                'booking_id' => $booking->id,
                                'user_id' => $booking->user->id
                            ]
                        ]
                    ],
                    'livemode' => false,
                    'pending_webhooks' => 1,
                    'request' => [
                        'id' => 'req_mock_' . uniqid(),
                        'idempotency_key' => null
                    ],
                    'type' => $eventType
                ];

            default:
                throw new \InvalidArgumentException("Unsupported event type: {$eventType}");
        }
    }
}
