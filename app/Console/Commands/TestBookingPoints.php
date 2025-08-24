<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Services\BookingPointsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestBookingPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booking-points {--user-id= : User ID to test with} {--booking-id= : Specific booking ID to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the booking points system';

    /**
     * Execute the console command.
     */
    public function handle(BookingPointsService $bookingPointsService)
    {
        $this->info('🚀 Testing Booking Points System');
        $this->newLine();

        // Get user to test with
        $userId = $this->option('user-id');
        $bookingId = $this->option('booking-id');

        if ($bookingId) {
            $this->testSpecificBooking($bookingId, $bookingPointsService);
        } elseif ($userId) {
            $this->testUserBookings($userId, $bookingPointsService);
        } else {
            $this->testAllBookings($bookingPointsService);
        }
    }

    private function testSpecificBooking($bookingId, BookingPointsService $bookingPointsService)
    {
        $this->info("📋 Testing specific booking ID: {$bookingId}");

        $booking = Booking::with(['user', 'vehicle'])->find($bookingId);

        if (!$booking) {
            $this->error("❌ Booking with ID {$bookingId} not found");
            return;
        }

        $this->displayBookingInfo($booking);
        $this->testPointsCalculation($booking, $bookingPointsService);
        $this->testPointsAddition($booking, $bookingPointsService);
    }

    private function testUserBookings($userId, BookingPointsService $bookingPointsService)
    {
        $this->info("👤 Testing bookings for user ID: {$userId}");

        $user = User::find($userId);
        if (!$user) {
            $this->error("❌ User with ID {$userId} not found");
            return;
        }

        $this->info("User: {$user->name} ({$user->email})");
        $this->info("PointSys Customer ID: " . ($user->pointsys_customer_id ?: 'Not registered'));
        $this->newLine();

        $bookings = $user->bookings()->with('vehicle')->get();

        if ($bookings->isEmpty()) {
            $this->warn("⚠️  No bookings found for this user");
            return;
        }

        $this->info("Found {$bookings->count()} bookings:");
        $this->newLine();

        foreach ($bookings as $booking) {
            $this->displayBookingInfo($booking);
            $this->testPointsCalculation($booking, $bookingPointsService);
            $this->newLine();
        }

        // Show user stats
        $stats = $bookingPointsService->getCustomerPointsStats($user);
        $this->info("📊 User Points Statistics:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Bookings with Points', $stats['total_bookings']],
                ['Total Points Earned', $stats['total_points_earned']],
                ['Total Days Rented', $stats['total_days_rented']],
                ['Points per Day', $stats['points_per_day']],
            ]
        );
    }

    private function testAllBookings(BookingPointsService $bookingPointsService)
    {
        $this->info("📋 Testing all confirmed bookings");

        $bookings = Booking::with(['user', 'vehicle'])
            ->where('status', 'confirmed')
            ->where('points_added_to_customer', false)
            ->get();

        if ($bookings->isEmpty()) {
            $this->warn("⚠️  No confirmed bookings found that need points added");
            return;
        }

        $this->info("Found {$bookings->count()} confirmed bookings without points:");
        $this->newLine();

        $tableData = [];
        foreach ($bookings as $booking) {
            $pointsToEarn = $bookingPointsService->calculatePointsForBooking($booking);
            $tableData[] = [
                $booking->id,
                $booking->user->name,
                $booking->vehicle->make . ' ' . $booking->vehicle->model,
                $booking->total_days,
                $pointsToEarn,
                $booking->status,
                $booking->points_added_to_customer ? 'Yes' : 'No'
            ];
        }

        $this->table(
            ['Booking ID', 'User', 'Vehicle', 'Days', 'Points to Earn', 'Status', 'Points Added'],
            $tableData
        );
    }

    private function displayBookingInfo(Booking $booking)
    {
        $this->info("📋 Booking #{$booking->id}");
        $this->line("   User: {$booking->user->name} ({$booking->user->email})");
        $this->line("   Vehicle: {$booking->vehicle->make} {$booking->vehicle->model}");
        $this->line("   Period: {$booking->start_date->format('d/m/Y')} - {$booking->end_date->format('d/m/Y')}");
        $this->line("   Days: {$booking->total_days}");
        $this->line("   Amount: AED {$booking->total_amount}");
        $this->line("   Status: {$booking->status}");
        $this->line("   Points Earned: {$booking->points_earned}");
        $this->line("   Points Added: " . ($booking->points_added_to_customer ? 'Yes' : 'No'));
    }

    private function testPointsCalculation(Booking $booking, BookingPointsService $bookingPointsService)
    {
        $calculatedPoints = $bookingPointsService->calculatePointsForBooking($booking);
        $this->info("   💰 Points Calculation: {$booking->total_days} days × 5 points = {$calculatedPoints} points");

        if ($booking->points_earned != $calculatedPoints) {
            $this->warn("   ⚠️  Mismatch: Stored points ({$booking->points_earned}) vs Calculated points ({$calculatedPoints})");
        } else {
            $this->info("   ✅ Points calculation matches stored value");
        }
    }

    private function testPointsAddition(Booking $booking, BookingPointsService $bookingPointsService)
    {
        if ($booking->points_added_to_customer) {
            $this->info("   ✅ Points already added to customer");
            return;
        }

        $this->info("   🔄 Testing points addition...");

        if (!$booking->user->pointsys_customer_id) {
            $this->warn("   ⚠️  User not registered in PointSys - cannot add points");
            return;
        }

        $result = $bookingPointsService->addPointsToCustomer($booking);

        if ($result['success']) {
            $this->info("   ✅ Points added successfully: {$result['points_added']} points");
            $this->info("   📝 Message: {$result['message']}");
        } else {
            $this->error("   ❌ Failed to add points: {$result['message']}");
            if (isset($result['error'])) {
                $this->error("   🔍 Error: {$result['error']}");
            }
        }
    }
}
