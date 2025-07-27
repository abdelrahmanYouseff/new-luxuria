<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PointSysService;
use App\Models\User;

class TestPointsAddition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'points:test {user_id? : The ID of the user to test} {--points=500 : Number of points to add}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test points addition functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pointSysService = app(PointSysService::class);

        $this->info('🔍 Testing Points Addition System...');
        $this->newLine();

        // Get user
        $userId = $this->argument('user_id');
        if (!$userId) {
            $userId = $this->ask('Enter user ID to test');
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("❌ User with ID {$userId} not found!");
            return;
        }

        $this->info("👤 Testing for user: {$user->name} ({$user->email})");

        // Check PointSys registration
        if (!$user->pointsys_customer_id) {
            $this->warn('⚠️  User not registered in PointSys. Registering now...');

            try {
                $customerData = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                ];

                $pointSysResponse = $pointSysService->registerCustomer($customerData);

                if ($pointSysResponse && isset($pointSysResponse['data']['customer_id'])) {
                    $user->update(['pointsys_customer_id' => $pointSysResponse['data']['customer_id']]);
                    $this->info('✅ User registered in PointSys successfully!');
                    $this->line('Customer ID: ' . $pointSysResponse['data']['customer_id']);
                } else {
                    $this->error('❌ Failed to register user in PointSys');
                    return;
                }
            } catch (\Exception $e) {
                $this->error('❌ Exception during PointSys registration: ' . $e->getMessage());
                return;
            }
        } else {
            $this->info('✅ User already registered in PointSys');
            $this->line('Customer ID: ' . $user->pointsys_customer_id);
        }

        $this->newLine();

        // Get current balance
        $this->info('📊 Getting current points balance...');
        try {
            $balanceResult = $pointSysService->getCustomerBalance($user->pointsys_customer_id);

            if ($balanceResult && isset($balanceResult['data']['points_balance'])) {
                $currentBalance = $balanceResult['data']['points_balance'];
                $this->line("Current Balance: {$currentBalance} points");
            } else {
                $this->warn('⚠️  Could not fetch current balance (will proceed anyway)');
                $currentBalance = 0;
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Error fetching balance: ' . $e->getMessage());
            $currentBalance = 0;
        }

        $this->newLine();

        // Add points
        $pointsToAdd = $this->option('points');
        $this->info("💰 Adding {$pointsToAdd} points...");

        try {
            $result = $pointSysService->addPointsToCustomerByUser(
                $user,
                $pointsToAdd,
                'Test points addition via console command',
                'TEST_POINTS_' . time()
            );

            if ($result && isset($result['status']) && $result['status'] === 'success') {
                $this->info('✅ SUCCESS: Points added successfully!');
                $this->newLine();

                $this->info('📈 Results:');
                $this->line('Points Added: ' . ($result['data']['points_added'] ?? $pointsToAdd));
                $this->line('New Balance: ' . ($result['data']['new_balance'] ?? 'Unknown'));
                $this->line('Transaction ID: ' . ($result['data']['transaction_id'] ?? 'N/A'));

                $this->newLine();
                $this->info('🎯 Points Addition System is working correctly!');

            } else {
                $this->error('❌ Failed to add points');
                $this->line('Response: ' . json_encode($result, JSON_PRETTY_PRINT));
            }

        } catch (\Exception $e) {
            $this->error('❌ Exception during points addition: ' . $e->getMessage());
        }

        $this->newLine();
        $this->comment('💡 To test with coupon purchase, go to: http://127.0.0.1:8001/coupons');
        $this->comment('   When you buy a coupon, 500 points will be added automatically!');
    }
}
