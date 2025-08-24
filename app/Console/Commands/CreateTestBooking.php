<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\BookingPointsService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateTestBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-booking {--user-id= : User ID to create booking for} {--vehicle-id= : Vehicle ID to book} {--days=3 : Number of days to book}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test booking to test the points system';

    /**
     * Execute the console command.
     */
    public function handle(BookingPointsService $bookingPointsService)
    {
        $this->info('🚗 Creating Test Booking');
        $this->newLine();

        $userId = $this->option('user-id');
        $vehicleId = $this->option('vehicle-id');
        $days = (int) $this->option('days');

        // Get or create user
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("❌ User with ID {$userId} not found");
                return;
            }
        } else {
            $user = User::first();
            if (!$user) {
                $this->error("❌ No users found in database");
                return;
            }
        }

        $this->info("👤 Using user: {$user->name} ({$user->email})");
        $this->info("🔗 PointSys Customer ID: " . ($user->pointsys_customer_id ?: 'Not registered'));

        // Get or create vehicle
        if ($vehicleId) {
            $vehicle = Vehicle::find($vehicleId);
            if (!$vehicle) {
                $this->error("❌ Vehicle with ID {$vehicleId} not found");
                return;
            }
        } else {
            $vehicle = Vehicle::first();
            if (!$vehicle) {
                $this->error("❌ No vehicles found in database");
                return;
            }
        }

        $this->info("🚗 Using vehicle: {$vehicle->make} {$vehicle->model} (ID: {$vehicle->id})");

        // Calculate dates
        $startDate = Carbon::now()->addDay();
        $endDate = $startDate->copy()->addDays($days - 1);
        $totalDays = $days;

        // Calculate amount
        $dailyRate = $vehicle->daily_rate ?? 100;
        $totalAmount = $dailyRate * $totalDays;

        // Calculate points
        $pointsToEarn = $totalDays * 5; // 5 points per day

        $this->info("📅 Booking period: {$startDate->format('d/m/Y')} - {$endDate->format('d/m/Y')}");
        $this->info("💰 Daily rate: AED {$dailyRate}");
        $this->info("💰 Total amount: AED {$totalAmount}");
        $this->info("🎯 Points to earn: {$pointsToEarn}");

        // Create booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'emirate' => 'Dubai',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'daily_rate' => $dailyRate,
            'pricing_type' => 'daily',
            'applied_rate' => $dailyRate,
            'total_days' => $totalDays,
            'total_amount' => $totalAmount,
            'points_earned' => $pointsToEarn,
            'status' => 'confirmed',
            'notes' => 'Test booking created via command'
        ]);

        $this->info("✅ Booking created successfully!");
        $this->info("📋 Booking ID: {$booking->id}");

        // Test points addition
        $this->newLine();
        $this->info("🎯 Testing points addition...");

        if (!$user->pointsys_customer_id) {
            $this->warn("⚠️  User not registered in PointSys - cannot add points");
            $this->info("💡 You can register the user using: php artisan test:points-addition --user-id={$user->id}");
        } else {
            $result = $bookingPointsService->addPointsToCustomer($booking);

            if ($result['success']) {
                $this->info("✅ Points added successfully: {$result['points_added']} points");
                $this->info("📝 Message: {$result['message']}");
            } else {
                $this->error("❌ Failed to add points: {$result['message']}");
                if (isset($result['error'])) {
                    $this->error("🔍 Error: {$result['error']}");
                }
            }
        }

        $this->newLine();
        $this->info("🎉 Test booking completed!");
        $this->info("📊 You can now test the system with:");
        $this->line("   php artisan test:booking-points --booking-id={$booking->id}");
        $this->line("   php artisan test:booking-points --user-id={$user->id}");
    }
}
