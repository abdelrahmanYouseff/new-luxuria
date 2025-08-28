<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingPointsService;
use App\Services\PointSysService;

class TestMobilePoints extends Command
{
    protected $signature = 'test:mobile-points {--user-id=} {--booking-id=} {--days=4}';
    protected $description = 'Test mobile points system';

    public function handle()
    {
        $this->info('🧪 Testing Mobile Points System...');

        // Get user
        $userId = $this->option('user-id');
        if (!$userId) {
            $user = User::where('role', 'user')->first();
            if (!$user) {
                $this->error('No user found. Please create a user first.');
                return 1;
            }
            $userId = $user->id;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $this->info("👤 Testing with user: {$user->name} ({$user->email})");

        // Get or create booking
        $bookingId = $this->option('booking-id');
        $days = (int) $this->option('days');

        if ($bookingId) {
            $booking = Booking::find($bookingId);
            if (!$booking) {
                $this->error("Booking with ID {$bookingId} not found.");
                return 1;
            }
        } else {
            // Get first available vehicle
            $vehicle = \App\Models\Vehicle::first();
            if (!$vehicle) {
                $this->error('No vehicles found in database.');
                return 1;
            }

            // Create test booking
            $booking = Booking::create([
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'emirate' => 'Dubai',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays($days)->format('Y-m-d'),
                'daily_rate' => 200,
                'pricing_type' => 'daily',
                'applied_rate' => 200,
                'total_days' => $days,
                'total_amount' => $days * 200,
                'points_earned' => $days * 5, // 5 points per day
                'points_added_to_customer' => false,
                'status' => 'pending',
                'notes' => 'Test booking for mobile points'
            ]);

            $this->info("📅 Created test booking for {$days} days");
        }

        $this->info("📊 Booking Details:");
        $this->line("   - ID: {$booking->id}");
        $this->line("   - Total Days: {$booking->total_days}");
        $this->line("   - Points Earned: {$booking->points_earned}");
        $this->line("   - Points Added: " . ($booking->points_added_to_customer ? 'Yes' : 'No'));

        // Test points calculation
        $bookingPointsService = new BookingPointsService(new PointSysService());
        $calculatedPoints = $bookingPointsService->calculatePointsForBooking($booking);

        $this->info("🧮 Points Calculation:");
        $this->line("   - Expected: {$days} days × 5 points = " . ($days * 5) . " points");
        $this->line("   - Calculated: {$calculatedPoints} points");
        $this->line("   - Match: " . ($calculatedPoints === ($days * 5) ? '✅' : '❌'));

        // Test adding points
        if (!$booking->points_added_to_customer) {
            $this->info("💳 Testing points addition...");

            $result = $bookingPointsService->addPointsToCustomer($booking);

            $this->info("📈 Points Addition Result:");
            $this->line("   - Success: " . ($result['success'] ? '✅' : '❌'));
            $this->line("   - Message: {$result['message']}");
            $this->line("   - Points Added: {$result['points_added']}");

            if (isset($result['transaction_id'])) {
                $this->line("   - Transaction ID: {$result['transaction_id']}");
            }
        } else {
            $this->info("✅ Points already added for this booking");
        }

        // Test stats
        $this->info("📊 Testing Points Statistics...");
        $stats = $bookingPointsService->getCustomerPointsStats($user);

        $this->info("📈 User Points Stats:");
        $this->line("   - Total Bookings: {$stats['total_bookings']}");
        $this->line("   - Total Points Earned: {$stats['total_points_earned']}");
        $this->line("   - Total Days Rented: {$stats['total_days_rented']}");
        $this->line("   - Points Per Day: {$stats['points_per_day']}");

        // Test history
        $this->info("📜 Testing Points History...");
        $history = $bookingPointsService->getBookingHistory($user);

        $this->info("📋 Points History:");
        $this->line("   - Total Bookings with Points: {$history['total_count']}");

        foreach ($history['bookings'] as $index => $bookingData) {
            $this->line("   - Booking #{$bookingData['booking_id']}: {$bookingData['points']['points_earned']} points");
        }

        $this->info("✅ Mobile Points System Test Completed!");

        return 0;
    }
}
