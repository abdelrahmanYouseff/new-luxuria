<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\PointSysService;

class FixAllUsersPointSys extends Command
{
    protected $signature = 'fix:all-users-pointsys {--force}';
    protected $description = 'Fix PointSys customer ID for all users';

    public function handle()
    {
        $this->info('🔧 Fixing PointSys Customer IDs for all users...');

        $users = User::where('role', 'user')->get();
        $this->info("📊 Found {$users->count()} users to process");

        $pointSysService = new PointSysService();
        $fixed = 0;
        $failed = 0;

        foreach ($users as $user) {
            $this->line("👤 Processing: {$user->name} ({$user->email})");
            $this->line("   Current PointSys ID: {$user->pointsys_customer_id}");

            // Skip if user already has a valid PointSys ID
            if ($user->pointsys_customer_id && $user->pointsys_customer_id !== '61') {
                $this->line("   ✅ Already has valid PointSys ID");
                continue;
            }

            // Try to register the user in PointSys
            $customerData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '0500000000'
            ];

            $result = $pointSysService->registerCustomer($customerData);

            if ($result && isset($result['status']) && $result['status'] === 'success') {
                $newCustomerId = $result['data']['customer_id'] ?? null;

                if ($newCustomerId) {
                    // Update user with new PointSys ID
                    $user->update(['pointsys_customer_id' => $newCustomerId]);

                    $this->line("   ✅ Registered successfully! New ID: {$newCustomerId}");
                    $fixed++;
                } else {
                    $this->line("   ❌ Failed to get customer ID from response");
                    $failed++;
                }
            } else {
                // Check if it's an email already exists error
                if ($result && isset($result['errors']['email'])) {
                    $this->line("   ⚠️ Email already exists in PointSys");

                    // For now, we'll skip these users
                    // In a real scenario, you might want to search for existing customers
                    $this->line("   💡 User needs manual PointSys ID assignment");
                } else {
                    $this->line("   ❌ Registration failed");
                    $this->line("   Response: " . json_encode($result));
                }
                $failed++;
            }

            $this->line(""); // Empty line for readability
        }

        $this->info("📈 Summary:");
        $this->line("   ✅ Fixed: {$fixed} users");
        $this->line("   ❌ Failed: {$failed} users");
        $this->line("   📊 Total: " . ($fixed + $failed) . " users processed");

        if ($failed > 0) {
            $this->warn("⚠️ Some users failed to register. They may need manual PointSys ID assignment.");
        }

        return 0;
    }
}
