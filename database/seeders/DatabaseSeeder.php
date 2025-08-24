<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // استدعاء SimpleAdminUserSeeder لإنشاء المستخدمين Admin
        $this->call([
            SimpleAdminUserSeeder::class,
        ]);

        // تم حذف كل عمليات إنشاء السيارات من السيدر بناءً على طلبك.
    }
}
