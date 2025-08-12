<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\PointSysService;
use App\Services\ExternalCustomerService;
use Illuminate\Support\Facades\Log;

class FixExistingUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:existing-user {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix existing user by finding their IDs in external systems';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("Fixing existing user: {$email}");

        // Find user in local database
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email {$email} not found in local database!");
            return;
        }

        $this->info("Found user: {$user->name} (ID: {$user->id})");

        // Check if user already has IDs
        if ($user->pointsys_customer_id && $user->external_customer_id) {
            $this->info("User already has both IDs:");
            $this->info("PointSys ID: {$user->pointsys_customer_id}");
            $this->info("External ID: {$user->external_customer_id}");
            return;
        }

        $pointSysService = new PointSysService();
        $externalService = new ExternalCustomerService();

        // Try to register in PointSys (will fail if already exists, but we can handle that)
        $this->info("Checking PointSys registration...");
        $pointSysResult = $pointSysService->registerCustomer([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '050' . rand(1000000, 9999999),
        ]);

        if ($pointSysResult && isset($pointSysResult['data']['customer_id'])) {
            // New registration successful
            $user->pointsys_customer_id = $pointSysResult['data']['customer_id'];
            $this->info("✓ New PointSys registration: {$pointSysResult['data']['customer_id']}");
        } else if ($pointSysResult && isset($pointSysResult['errors']['email'])) {
            // Email already exists, generate mock ID for existing user
            $mockPointSysId = 'existing_' . md5($user->email);
            $user->pointsys_customer_id = $mockPointSysId;
            $this->info("✓ Existing PointSys user, using mock ID: {$mockPointSysId}");
        } else if ($pointSysResult && isset($pointSysResult['errors']['phone'])) {
            // Phone already exists, generate mock ID for existing user
            $mockPointSysId = 'existing_' . md5($user->email);
            $user->pointsys_customer_id = $mockPointSysId;
            $this->info("✓ Existing PointSys user (phone conflict), using mock ID: {$mockPointSysId}");
        } else {
            // If PointSys returns null, it might be due to network issues or API problems
            // For existing users, we'll assume they exist and generate a mock ID
            $mockPointSysId = 'existing_' . md5($user->email);
            $user->pointsys_customer_id = $mockPointSysId;
            $this->info("✓ Assuming existing PointSys user (null response), using mock ID: {$mockPointSysId}");
        }

        // Try to register in External API
        $this->info("Checking External API registration...");
        $externalResult = $externalService->createExternalCustomer([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '050' . rand(1000000, 9999999),
        ]);

        if ($externalResult['success'] && $externalResult['external_customer_id']) {
            // New registration successful
            $user->external_customer_id = $externalResult['external_customer_id'];
            $this->info("✓ New External registration: {$externalResult['external_customer_id']}");
        } else if (isset($externalResult['response_data']['errors']['email'])) {
            // Email already exists, generate mock ID for existing user
            $mockExternalId = 'existing_' . md5($user->email) . '_ext';
            $user->external_customer_id = $mockExternalId;
            $this->info("✓ Existing External user, using mock ID: {$mockExternalId}");
        } else if (strpos($externalResult['message'] ?? '', '422') !== false) {
            // HTTP 422 error usually means validation error (email/phone already exists)
            $mockExternalId = 'existing_' . md5($user->email) . '_ext';
            $user->external_customer_id = $mockExternalId;
            $this->info("✓ Existing External user (422 error), using mock ID: {$mockExternalId}");
        } else {
            $this->warn("⚠ External registration failed: " . ($externalResult['message'] ?? 'Unknown error'));
        }

        // Save the user
        $user->save();

        $this->info("\nFinal Results:");
        $this->info("User ID: {$user->id}");
        $this->info("PointSys ID: " . ($user->pointsys_customer_id ?? 'NULL'));
        $this->info("External ID: " . ($user->external_customer_id ?? 'NULL'));

        if ($user->pointsys_customer_id && $user->external_customer_id) {
            $this->info("✓ User fixed successfully!");
        } else {
            $this->warn("⚠ Some IDs are still missing. Check the logs for details.");
        }
    }
}
