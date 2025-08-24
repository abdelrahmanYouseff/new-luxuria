<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class TestServerLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:server-login {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test server login functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info("🔍 Testing server login for: {$email}");

        // Test database connection
        try {
            DB::connection()->getPdo();
            $this->info("✅ Database connection: OK");
        } catch (\Exception $e) {
            $this->error("❌ Database connection failed: " . $e->getMessage());
            return 1;
        }

        // Test sessions table
        if (DB::getSchemaBuilder()->hasTable('sessions')) {
            $this->info("✅ Sessions table: EXISTS");
        } else {
            $this->error("❌ Sessions table: MISSING");
            return 1;
        }

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

        // Test session configuration
        $this->info("📝 Session driver: " . config('session.driver'));
        $this->info("📝 Session lifetime: " . config('session.lifetime') . " minutes");

        // Simulate web login
        Session::start();

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->info("✅ Server authentication successful!");
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

            // Test session storage
            if (config('session.driver') === 'database') {
                $sessionCount = DB::table('sessions')->count();
                $this->info("📊 Sessions in database: {$sessionCount}");
            }

            Auth::logout();
            Session::flush();
            $this->info("✅ Logged out and session cleared");

            return 0;
        } else {
            $this->error("❌ Server authentication failed!");

            // Additional debugging
            $this->info("🔍 Debugging info:");
            $this->info("- Session driver: " . config('session.driver'));
            $this->info("- Session lifetime: " . config('session.lifetime'));
            $this->info("- Session encrypt: " . (config('session.encrypt') ? 'YES' : 'NO'));

            return 1;
        }
    }
}
