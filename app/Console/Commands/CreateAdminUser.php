<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {email?} {password?} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@rentluxuria.com';
        $password = $this->argument('password') ?? 'password123';
        $name = $this->argument('name') ?? 'Admin User';

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Create the admin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'emirate' => 'Dubai', // Default emirate
            'address' => 'Admin Address', // Default address
            'email_verified_at' => now(),
        ]);

        $this->info("✅ Admin user created successfully!");
        $this->line("📧 Email: {$user->email}");
        $this->line("👤 Name: {$user->name}");
        $this->line("🔑 Role: {$user->role}");
        $this->line("🆔 User ID: {$user->id}");

        return 0;
    }
}
