<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class TestServerRedirect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:server-redirect {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test server redirect after login';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info("🔍 Testing server redirect for: {$email}");

        // Find user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("❌ User not found!");
            return 1;
        }

        $this->info("✅ User found: {$user->name} (Role: {$user->role})");

        // Test password
        if (!Hash::check($password, $user->password)) {
            $this->error("❌ Invalid password!");
            return 1;
        }

        $this->info("✅ Password is correct!");

        // Simulate login process
        Session::start();

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->info("✅ Authentication successful!");

            // Test redirect logic
            $this->info("📝 Testing redirect logic...");

            // Check if user is admin
            if (Auth::user()->role === 'admin') {
                $this->info("🎯 User is admin");
                $dashboardUrl = route('dashboard');
                $this->info("📍 Should redirect to: {$dashboardUrl}");

                // Check if dashboard route exists
                try {
                    $response = app('router')->getRoutes()->match(
                        app('request')->create($dashboardUrl, 'GET')
                    );
                    $this->info("✅ Dashboard route: EXISTS");
                } catch (\Exception $e) {
                    $this->error("❌ Dashboard route: NOT FOUND");
                    $this->error("Error: " . $e->getMessage());
                }

                // Check if user can access dashboard
                $this->info("🔐 Testing dashboard access...");
                try {
                    $dashboardController = app('App\Http\Controllers\DashboardController');
                    $this->info("✅ Dashboard controller: EXISTS");
                } catch (\Exception $e) {
                    $this->info("ℹ️ Dashboard controller: NOT FOUND (using Inertia)");
                }

            } else {
                $this->info("👤 User is regular");
                $homeUrl = route('home');
                $this->info("📍 Should redirect to: {$homeUrl}");
            }

            // Test session data
            $this->info("📝 Session data:");
            $this->info("- Session ID: " . Session::getId());
            $this->info("- User ID in session: " . Session::get('auth.user_id'));
            $this->info("- Auth check: " . (Auth::check() ? 'YES' : 'NO'));

            // Test middleware
            $this->info("🔐 Testing middleware...");
            try {
                $middleware = app('router')->getMiddleware();
                $this->info("✅ Middleware loaded: " . count($middleware) . " items");
            } catch (\Exception $e) {
                $this->error("❌ Middleware error: " . $e->getMessage());
            }

            Auth::logout();
            Session::flush();
            $this->info("✅ Logged out and session cleared");

            return 0;
        } else {
            $this->error("❌ Authentication failed!");
            return 1;
        }
    }
}
