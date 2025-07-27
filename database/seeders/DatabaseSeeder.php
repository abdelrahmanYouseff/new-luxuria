<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users
        \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@luxuria.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // تم حذف كل عمليات إنشاء السيارات من السيدر بناءً على طلبك.
    }
}
