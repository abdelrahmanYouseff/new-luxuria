<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:environment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check environment settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Checking environment settings...");

        // Check environment
        $environment = app()->environment();
        $this->info("📝 Environment: {$environment}");

        // Check APP_ENV
        $appEnv = env('APP_ENV');
        $this->info("📝 APP_ENV: {$appEnv}");

        // Check APP_DEBUG
        $appDebug = env('APP_DEBUG');
        $this->info("📝 APP_DEBUG: {$appDebug}");

        // Check APP_URL
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

        // Check if we're in production
        if ($environment === 'production') {
            $this->info("✅ Environment is production - HTTPS will be forced");
        } else {
            $this->info("⚠️ Environment is not production - HTTPS will not be forced");
            $this->info("💡 To force HTTPS, set APP_ENV=production in .env");
        }

        return 0;
    }
}
