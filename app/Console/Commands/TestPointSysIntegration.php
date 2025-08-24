<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PointSysService;
use App\Services\BookingPointsService;
use Illuminate\Console\Command;

class TestPointSysIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pointsys-integration {--user-id= : User ID to test with} {--add-points= : Number of points to add for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PointSys integration and points system';

    /**
     * Execute the console command.
     */
    public function handle(PointSysService $pointSysService, BookingPointsService $bookingPointsService)
    {
        $this->info('🔗 Testing PointSys Integration');
        $this->newLine();

        $userId = $this->option('user-id');
        $addPoints = (int) $this->option('add-points');

        // Get user
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("❌ User with ID {$userId} not found");
                return;
            }
        } else {
            $user = User::whereNotNull('pointsys_customer_id')->first();
            if (!$user) {
                $this->error("❌ No users with PointSys customer ID found");
                return;
            }
        }

        $this->info("👤 Testing with user: {$user->name} ({$user->email})");
        $this->info("🔗 PointSys Customer ID: {$user->pointsys_customer_id}");
        $this->newLine();

        // Test 1: Get current balance
        $this->info("📊 Test 1: Getting current balance...");
        try {
            $balance = $pointSysService->getCustomerBalance($user->pointsys_customer_id);

            if ($balance && isset($balance['status']) && $balance['status'] === 'success') {
                $this->info("✅ Balance retrieved successfully");
                $this->line("   Current Balance: {$balance['data']['points_balance']} points");
                $this->line("   Total Earned: {$balance['data']['total_earned']} points");
                $this->line("   Total Redeemed: {$balance['data']['total_redeemed']} points");
                $this->line("   Tier: {$balance['data']['tier']}");
            } else {
                $this->error("❌ Failed to get balance");
                $this->line("   Response: " . json_encode($balance));
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception getting balance: {$e->getMessage()}");
        }

        $this->newLine();

        // Test 2: Add test points
        if ($addPoints > 0) {
            $this->info("➕ Test 2: Adding {$addPoints} test points...");
            try {
                $result = $pointSysService->addPointsToCustomer(
                    $user->pointsys_customer_id,
                    $addPoints,
                    'Test points from command line',
                    'TEST_CMD_' . time()
                );

                if ($result && isset($result['status']) && $result['status'] === 'success') {
                    $this->info("✅ Test points added successfully");
                    $this->line("   Points Added: {$result['data']['points_added']} points");
                    $this->line("   New Balance: {$result['data']['new_balance']} points");
                    $this->line("   Transaction ID: {$result['data']['transaction_id']}");
                } else {
                    $this->error("❌ Failed to add test points");
                    $this->line("   Response: " . json_encode($result));
                }
            } catch (\Exception $e) {
                $this->error("❌ Exception adding test points: {$e->getMessage()}");
            }

            $this->newLine();
        }

        // Test 3: Get updated balance
        $this->info("📊 Test 3: Getting updated balance...");
        try {
            $updatedBalance = $pointSysService->getCustomerBalance($user->pointsys_customer_id);

            if ($updatedBalance && isset($updatedBalance['status']) && $updatedBalance['status'] === 'success') {
                $this->info("✅ Updated balance retrieved successfully");
                $this->line("   Updated Balance: {$updatedBalance['data']['points_balance']} points");

                if ($addPoints > 0) {
                    $expectedBalance = ($balance['data']['points_balance'] ?? 0) + $addPoints;
                    if ($updatedBalance['data']['points_balance'] == $expectedBalance) {
                        $this->info("✅ Balance updated correctly");
                    } else {
                        $this->warn("⚠️  Balance mismatch - Expected: {$expectedBalance}, Got: {$updatedBalance['data']['points_balance']}");
                    }
                }
            } else {
                $this->error("❌ Failed to get updated balance");
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception getting updated balance: {$e->getMessage()}");
        }

        $this->newLine();

        // Test 4: Booking points statistics
        $this->info("📋 Test 4: Booking points statistics...");
        try {
            $stats = $bookingPointsService->getCustomerPointsStats($user);
            $this->info("✅ Booking points stats retrieved");
            $this->line("   Total Bookings: {$stats['total_bookings']}");
            $this->line("   Total Points Earned: {$stats['total_points_earned']} points");
            $this->line("   Total Days Rented: {$stats['total_days_rented']} days");
            $this->line("   Points per Day: {$stats['points_per_day']}");

            // Compare with PointSys balance
            $pointSysBalance = $updatedBalance['data']['points_balance'] ?? $balance['data']['points_balance'] ?? 0;
            $bookingPoints = $stats['total_points_earned'];

            if ($pointSysBalance >= $bookingPoints) {
                $this->info("✅ PointSys balance includes booking points");
                $this->line("   PointSys Balance: {$pointSysBalance} points");
                $this->line("   Booking Points: {$bookingPoints} points");
                if ($pointSysBalance > $bookingPoints) {
                    $extraPoints = $pointSysBalance - $bookingPoints;
                    $this->line("   Extra Points (from other sources): {$extraPoints} points");
                }
            } else {
                $this->warn("⚠️  PointSys balance is less than booking points");
                $this->line("   PointSys Balance: {$pointSysBalance} points");
                $this->line("   Booking Points: {$bookingPoints} points");
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception getting booking stats: {$e->getMessage()}");
        }

        $this->newLine();
        $this->info("🎉 PointSys Integration Test Completed!");
    }
}
