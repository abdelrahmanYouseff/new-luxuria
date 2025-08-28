<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\BookingPointsService;
use App\Services\PointSysService;
use Carbon\Carbon;

class TestMobileBooking extends Command
{
    protected $signature = 'test:mobile-booking {--user-id=} {--vehicle-id=} {--days=3}';
    protected $description = 'Test complete mobile booking flow with points';

    public function handle()
    {
        $this->info('🧪 Testing Complete Mobile Booking Flow...');

        $userId = $this->option('user-id');
        $vehicleId = $this->option('vehicle-id');
        $days = $this->option('days');

        // Get user
        if (!$userId) {
            $user = User::where('role', 'user')->first();
            if (!$user) {
                $this->error('No user found. Please provide --user-id');
                return 1;
            }
            $userId = $user->id;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        // Get vehicle
        if (!$vehicleId) {
            $vehicle = Vehicle::where('is_visible', true)->first();
            if (!$vehicle) {
                $this->error('No available vehicle found. Please provide --vehicle-id');
                return 1;
            }
            $vehicleId = $vehicle->id;
        }

        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle) {
            $this->error("Vehicle with ID {$vehicleId} not found.");
            return 1;
        }

        $this->info("👤 User: {$user->name} ({$user->email})");
        $this->info("🔗 PointSys ID: {$user->pointsys_customer_id}");
        $this->info("🚗 Vehicle: {$vehicle->make} {$vehicle->model} ({$vehicle->year})");
        $this->info("📅 Days: {$days}");

        // Calculate dates
        $startDate = Carbon::now()->addDay()->format('Y-m-d');
        $endDate = Carbon::now()->addDays($days + 1)->format('Y-m-d');
        $totalDays = $days;

        // Calculate points
        $pointsToEarn = $totalDays * 5;

        $this->info("📅 Start Date: {$startDate}");
        $this->info("📅 End Date: {$endDate}");
        $this->info("📊 Points to earn: {$pointsToEarn}");

        // Step 1: Create booking (simulate mobile app)
        $this->info("\n📝 Step 1: Creating booking...");

        $booking = Booking::create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'daily_rate' => $vehicle->daily_rate,
            'applied_rate' => $vehicle->daily_rate,
            'total_amount' => $vehicle->daily_rate * $totalDays,
            'status' => 'pending',
            'payment_status' => 'pending',
            'emirate' => 'Dubai',
            'points_earned' => $pointsToEarn,
            'points_added_to_customer' => false,
            'external_reservation_id' => 'TEST_' . time(),
            'external_reservation_uid' => 'TEST_UID_' . time()
        ]);

        $this->info("✅ Booking created! ID: {$booking->id}");
        $this->info("💰 Total amount: {$booking->total_amount} AED");

        // Step 2: Simulate payment confirmation
        $this->info("\n💳 Step 2: Confirming payment...");

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $this->info("✅ Payment confirmed!");

        // Step 3: Add points
        $this->info("\n🎯 Step 3: Adding points to PointSys...");

        $bookingPointsService = new BookingPointsService(new PointSysService());
        $pointsResult = $bookingPointsService->addPointsToCustomer($booking);

        if ($pointsResult['success']) {
            $this->info("✅ Points added successfully!");
            $this->info("📈 Points added: {$pointsResult['points_added']}");
            if (isset($pointsResult['transaction_id'])) {
                $this->info("🆔 Transaction ID: {$pointsResult['transaction_id']}");
            }
        } else {
            $this->error("❌ Failed to add points: {$pointsResult['message']}");
        }

        // Step 4: Check PointSys balance
        $this->info("\n🔗 Step 4: Checking PointSys balance...");

        if ($user->pointsys_customer_id) {
            $pointSysService = new PointSysService();
            $balance = $pointSysService->getCustomerBalance($user->pointsys_customer_id);

            if ($balance && isset($balance['data'])) {
                $points = $balance['data']['points_balance'] ?? 0;
                $this->info("✅ PointSys balance: {$points} points");
            } else {
                $this->warn("⚠️ Could not fetch PointSys balance");
            }
        } else {
            $this->warn("⚠️ User has no PointSys ID");
        }

        // Step 5: Final verification
        $this->info("\n📊 Step 5: Final verification...");

        $booking->refresh();
        $this->info("📈 Booking status: {$booking->status}");
        $this->info("💳 Payment status: {$booking->payment_status}");
        $this->info("🎯 Points earned: {$booking->points_earned}");
        $this->info("✅ Points added to customer: " . ($booking->points_added_to_customer ? 'Yes' : 'No'));

        // Step 6: Test API response format
        $this->info("\n📱 Step 6: Testing API response format...");

        $responseData = [
            'success' => true,
            'message' => 'تم تأكيد الدفع وتحديث حالة الحجز بنجاح في جميع الأنظمة',
            'data' => [
                'booking' => [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'vehicle' => $vehicle->make . ' ' . $vehicle->model,
                    'dates' => [
                        'start_date' => $booking->start_date,
                        'end_date' => $booking->end_date,
                        'total_days' => $booking->total_days
                    ],
                    'amount' => $booking->total_amount,
                    'points' => [
                        'points_earned' => $booking->points_earned,
                        'points_added_to_customer' => $booking->points_added_to_customer,
                        'points_per_day' => 5
                    ]
                ],
                'points_added' => [
                    'success' => $pointsResult['success'],
                    'message' => $pointsResult['message'],
                    'points_earned' => $booking->points_earned,
                    'points_added_to_customer' => $booking->points_added_to_customer
                ]
            ]
        ];

        $this->info("📋 API Response Structure:");
        $this->line(json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("\n✅ Complete mobile booking test completed!");
        $this->info("🎯 Summary:");
        $this->info("   - Booking created: {$booking->id}");
        $this->info("   - Payment confirmed: Yes");
        $this->info("   - Points added: " . ($pointsResult['success'] ? 'Yes' : 'No'));
        $this->info("   - Points earned: {$booking->points_earned}");
        $this->info("   - Total days: {$booking->total_days}");

        return 0;
    }
}
