<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestFrontendLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:frontend-login {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test frontend login via HTTP request';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info("🌐 Testing frontend login for: {$email}");

        // Get CSRF token first
        $this->info("📝 Getting CSRF token...");
        $response = Http::get('https://rentluxuria.com/login');

        if ($response->successful()) {
            $this->info("✅ Login page accessible");

            // Extract CSRF token from response
            preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response->body(), $matches);
            $csrfToken = $matches[1] ?? null;

            if ($csrfToken) {
                $this->info("✅ CSRF token found: " . substr($csrfToken, 0, 20) . "...");

                // Attempt login
                $this->info("📝 Attempting login...");
                $loginResponse = Http::asForm()->post('https://rentluxuria.com/login', [
                    'email' => $email,
                    'password' => $password,
                    '_token' => $csrfToken,
                ]);

                $this->info("📊 Response status: " . $loginResponse->status());
                $this->info("📊 Response headers: " . json_encode($loginResponse->headers(), JSON_PRETTY_PRINT));

                if ($loginResponse->redirect()) {
                    $this->info("✅ Redirect detected!");
                    $this->info("📍 Redirect URL: " . $loginResponse->effectiveUri());

                    // Check if redirect is to dashboard
                    if (str_contains($loginResponse->effectiveUri(), 'dashboard')) {
                        $this->info("🎯 Success! Redirected to dashboard");
                    } else {
                        $this->info("⚠️ Redirected to: " . $loginResponse->effectiveUri());
                    }
                } else {
                    $this->error("❌ No redirect detected");
                    $this->info("📄 Response body: " . substr($loginResponse->body(), 0, 500) . "...");
                }

            } else {
                $this->error("❌ CSRF token not found");
            }

        } else {
            $this->error("❌ Login page not accessible. Status: " . $response->status());
        }

        return 0;
    }
}
