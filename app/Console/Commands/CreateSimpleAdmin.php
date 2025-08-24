<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSimpleAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:simple {email} {password} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a simple admin user (without phone field)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name') ?? 'Admin User';

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("❌ User with email {$email} already exists!");
            return 1;
        }

        // Create the admin user (without phone field)
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'emirate' => 'Dubai',
            'address' => 'Dubai, UAE',
            'email_verified_at' => now(),
        ]);

        $this->info("✅ Admin user created successfully!");
        $this->line("📧 Email: {$user->email}");
        $this->line("👤 Name: {$user->name}");
        $this->line("🔑 Password: {$password}");
        $this->line("🔑 Role: {$user->role}");
        $this->line("🆔 User ID: {$user->id}");

        return 0;
    }
}
