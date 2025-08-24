<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // قائمة المستخدمين Admin
        $adminUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@luxuria.com',
                'password' => 'admin123',
                'role' => 'admin',
                'phone' => '+971501234567',
                'emirate' => 'Dubai',
                'address' => 'Dubai, UAE',
            ],
            [
                'name' => 'Rent Luxuria Admin',
                'email' => 'admin@rentluxuria.com',
                'password' => 'password123',
                'role' => 'admin',
                'phone' => '+971502345678',
                'emirate' => 'Abu Dhabi',
                'address' => 'Abu Dhabi, UAE',
            ],
            [
                'name' => 'Luxuria UAE Admin',
                'email' => 'admin@luxuria-uae.com',
                'password' => 'admin123456',
                'role' => 'admin',
                'phone' => '+971503456789',
                'emirate' => 'Sharjah',
                'address' => 'Sharjah, UAE',
            ],
            [
                'name' => 'Test Admin User',
                'email' => 'test@admin.com',
                'password' => 'test123',
                'role' => 'admin',
                'phone' => '+971504567890',
                'emirate' => 'Ajman',
                'address' => 'Ajman, UAE',
            ],
            [
                'name' => 'System Administrator',
                'email' => 'system@luxuria.com',
                'password' => 'system123',
                'role' => 'admin',
                'phone' => '+971505678901',
                'emirate' => 'Ras Al Khaimah',
                'address' => 'Ras Al Khaimah, UAE',
            ],
        ];

        foreach ($adminUsers as $adminData) {
            User::firstOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => Hash::make($adminData['password']),
                    'role' => $adminData['role'],
                    'phone' => $adminData['phone'],
                    'emirate' => $adminData['emirate'],
                    'address' => $adminData['address'],
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Admin users seeded successfully!');
        $this->command->info('Available admin accounts:');

        foreach ($adminUsers as $admin) {
            $this->command->info("- {$admin['email']} / {$admin['password']}");
        }
    }
}
