<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FixHttpsRedirect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:https-redirect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix HTTPS redirect issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔒 Fixing HTTPS redirect issues...");

        // Check current environment
        $this->info("📝 Current environment: " . app()->environment());

        // Check APP_URL in .env
        $appUrl = env('APP_URL');
        $this->info("📝 APP_URL: {$appUrl}");

        // Check if HTTPS is forced
        $forceHttps = env('FORCE_HTTPS', false);
        $this->info("📝 FORCE_HTTPS: " . ($forceHttps ? 'YES' : 'NO'));

        // Test routes
        $this->info("📝 Testing routes...");
        $dashboardUrl = route('dashboard');
        $homeUrl = route('home');

        $this->info("📍 Dashboard URL: {$dashboardUrl}");
        $this->info("📍 Home URL: {$homeUrl}");

        // Check if URLs are HTTPS
        if (str_contains($dashboardUrl, 'https://')) {
            $this->info("✅ Dashboard URL is HTTPS");
        } else {
            $this->info("⚠️ Dashboard URL is not HTTPS");
        }

        if (str_contains($homeUrl, 'https://')) {
            $this->info("✅ Home URL is HTTPS");
        } else {
            $this->info("⚠️ Home URL is not HTTPS");
        }

        // Clear caches
        $this->info("📝 Clearing caches...");
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        $this->info("✅ Caches cleared");

        // Test login redirect
        $this->info("📝 Testing login redirect...");
        $result = Artisan::call('test:server-redirect', [
            'email' => 'admin@rentluxuria.com',
            'password' => 'password123'
        ]);

        if ($result === 0) {
            $this->info("✅ Login redirect test: PASSED");
        } else {
            $this->error("❌ Login redirect test: FAILED");
        }

        $this->info("🎯 HTTPS redirect fix completed!");
        $this->info("🌐 Test at: https://rentluxuria.com/test-login");

        return 0;
    }
}
