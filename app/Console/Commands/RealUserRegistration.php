<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\PointSysService;
use App\Services\ExternalCustomerService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RealUserRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'real:user-registration {email} {name} {phone?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register user with real APIs (no mock data)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $phone = $this->argument('phone') ?? '050' . rand(1000000, 9999999);

        $this->info("Real user registration for: {$name} ({$email})");
        $this->info("Phone: {$phone}");

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->warn("User with email {$email} already exists!");
            $this->info("PointSys ID: " . ($existingUser->pointsys_customer_id ?? 'NULL'));
            $this->info("External ID: " . ($existingUser->external_customer_id ?? 'NULL'));
            return;
        }

        // Create user in local database
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password123'),
            'emirate' => 'Dubai',
            'address' => 'Test Address',
            'phone' => $phone,
        ]);

        $this->info("User created in local database with ID: {$user->id}");

        // Register in External API
        $this->info("Registering in External API...");
        $externalService = new ExternalCustomerService();
        $externalResult = $externalService->createExternalCustomer([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        if ($externalResult['success'] && $externalResult['external_customer_id']) {
            $user->update([
                'external_customer_id' => $externalResult['external_customer_id']
            ]);
            $this->info("✓ Registered in External API: {$externalResult['external_customer_id']}");
        } else {
            $this->error("✗ Failed to register in External API: " . ($externalResult['message'] ?? 'Unknown error'));
        }

        // Register in PointSys
        $this->info("Registering in PointSys...");
        $pointSysService = new PointSysService();
        $pointSysResult = $pointSysService->registerCustomer([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        if ($pointSysResult && isset($pointSysResult['data']['customer_id'])) {
            $user->update([
                'pointsys_customer_id' => $pointSysResult['data']['customer_id']
            ]);
            $this->info("✓ Registered in PointSys: {$pointSysResult['data']['customer_id']}");
        } else {
            $this->error("✗ Failed to register in PointSys: " . json_encode($pointSysResult));
        }

        // Final check
        $user->refresh();
        $this->info("\nFinal Results:");
        $this->info("User ID: {$user->id}");
        $this->info("PointSys ID: " . ($user->pointsys_customer_id ?? 'NULL'));
        $this->info("External ID: " . ($user->external_customer_id ?? 'NULL'));

        if ($user->pointsys_customer_id && $user->external_customer_id) {
            $this->info("✓ Registration completed successfully!");
        } else {
            $this->warn("⚠ Some registrations failed. Check the logs for details.");
        }
    }
}
