<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class TestWebLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:web-login {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test web login functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info("Testing web login for: {$email}");

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

        // Simulate web login
        Session::start();

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->info("✅ Web authentication successful!");
            $this->info("👤 Logged in as: " . Auth::user()->name);
            $this->info("🔑 Role: " . Auth::user()->role);
            $this->info("🆔 User ID: " . Auth::user()->id);

            // Test session
            $this->info("📝 Session ID: " . Session::getId());
            $this->info("🔐 CSRF Token: " . csrf_token());

            // Test role-based redirect
            if (Auth::user()->role === 'admin') {
                $this->info("🎯 User is admin - should redirect to: " . route('dashboard'));
            } else {
                $this->info("👤 User is regular - should redirect to: " . route('home'));
            }

            Auth::logout();
            Session::flush();
            $this->info("✅ Logged out and session cleared");

            return 0;
        } else {
            $this->error("❌ Web authentication failed!");
            return 1;
        }
    }
}
