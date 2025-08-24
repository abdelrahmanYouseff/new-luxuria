<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TestSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:session {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test session and authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info("Testing session for: {$email}");

        // Test authentication
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            $this->info("✅ Authentication successful!");
            $this->info("👤 User: {$user->name} (Role: {$user->role})");
            $this->info("🆔 User ID: {$user->id}");

            // Test session
            Session::regenerate();
            $this->info("✅ Session regenerated");

            // Check if user is still authenticated
            if (Auth::check()) {
                $this->info("✅ User is still authenticated");

                // Test role-based redirect logic
                if ($user->role === 'admin') {
                    $this->info("🎯 User is admin - should redirect to: " . route('dashboard'));
                } else {
                    $this->info("👤 User is regular - should redirect to: " . route('home'));
                }
            } else {
                $this->error("❌ User is not authenticated after session regeneration");
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
