<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SimpleAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // قائمة المستخدمين Admin (بدون phone)
        $adminUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@luxuria.com',
                'password' => 'admin123',
                'role' => 'admin',
                'emirate' => 'Dubai',
                'address' => 'Dubai, UAE',
            ],
            [
                'name' => 'Rent Luxuria Admin',
                'email' => 'admin@rentluxuria.com',
                'password' => 'password123',
                'role' => 'admin',
                'emirate' => 'Abu Dhabi',
                'address' => 'Abu Dhabi, UAE',
            ],
            [
                'name' => 'Luxuria UAE Admin',
                'email' => 'admin@luxuria-uae.com',
                'password' => 'admin123456',
                'role' => 'admin',
                'emirate' => 'Sharjah',
                'address' => 'Sharjah, UAE',
            ],
            [
                'name' => 'Test Admin User',
                'email' => 'test@admin.com',
                'password' => 'test123',
                'role' => 'admin',
                'emirate' => 'Ajman',
                'address' => 'Ajman, UAE',
            ],
            [
                'name' => 'System Administrator',
                'email' => 'system@luxuria.com',
                'password' => 'system123',
                'role' => 'admin',
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
                    'emirate' => $adminData['emirate'],
                    'address' => $adminData['address'],
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Admin users seeded successfully!');
        $this->command->info('📋 Available admin accounts:');
        
        foreach ($adminUsers as $admin) {
            $this->command->info("- {$admin['email']} / {$admin['password']}");
        }
    }
}
