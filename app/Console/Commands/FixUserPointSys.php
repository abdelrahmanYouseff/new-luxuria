<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\PointSysService;

class FixUserPointSys extends Command
{
    protected $signature = 'fix:user-pointsys {--user-id=} {--email=}';
    protected $description = 'Fix PointSys customer ID for user';

    public function handle()
    {
        $this->info('🔧 Fixing PointSys Customer ID...');

        // Get user
        $userId = $this->option('user-id');
        $email = $this->option('email');

        if ($userId) {
            $user = User::find($userId);
        } elseif ($email) {
            $user = User::where('email', $email)->first();
        } else {
            $user = User::where('role', 'user')->first();
        }

        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        $this->info("👤 User: {$user->name} ({$user->email})");
        $this->info("📊 Current PointSys ID: {$user->pointsys_customer_id}");

        // Initialize PointSys service
        $pointSysService = new PointSysService();

        // Try to register the user in PointSys
        $customerData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '0500000000'
        ];

        $this->info("🔄 Registering user in PointSys...");

        $result = $pointSysService->registerCustomer($customerData);

        if ($result && isset($result['status']) && $result['status'] === 'success') {
            $newCustomerId = $result['data']['customer_id'] ?? null;

            if ($newCustomerId) {
                // Update user with new PointSys ID
                $user->update(['pointsys_customer_id' => $newCustomerId]);

                $this->info("✅ User registered successfully in PointSys!");
                $this->info("🆔 New PointSys ID: {$newCustomerId}");

                // Test adding points
                $this->info("🧪 Testing points addition...");
                $pointsResult = $pointSysService->addPointsToCustomer(
                    $newCustomerId,
                    10,
                    'Test points addition',
                    'TEST_' . time()
                );

                if ($pointsResult && isset($pointsResult['status']) && $pointsResult['status'] === 'success') {
                    $this->info("✅ Points addition test successful!");
                } else {
                    $this->warn("⚠️ Points addition test failed: " . json_encode($pointsResult));
                }

            } else {
                $this->error("❌ Failed to get customer ID from PointSys response");
                $this->line("Response: " . json_encode($result, JSON_PRETTY_PRINT));
            }
        } else {
            $this->error("❌ Failed to register user in PointSys");
            $this->line("Response: " . json_encode($result, JSON_PRETTY_PRINT));
        }

        return 0;
    }
}
