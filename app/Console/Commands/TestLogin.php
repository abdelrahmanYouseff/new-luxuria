<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:login {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test login functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info("Testing login for: {$email}");

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

        // Test authentication
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->info("✅ Authentication successful!");

            // Check role-based redirect
            if ($user->role === 'admin') {
                $this->info("🎯 User is admin - should redirect to dashboard");
            } else {
                $this->info("👤 User is regular - should redirect to home");
            }

            Auth::logout();
            return 0;
        } else {
            $this->error("❌ Authentication failed!");
            return 1;
        }
    }
}
