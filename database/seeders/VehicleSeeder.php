<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'api_id' => '1',
                'plate_number' => 'CC-51054',
                'status' => 'available',
                'ownership_status' => 'owned',
                'make' => 'BMW',
                'model' => '7 Series 740i',
                'year' => '2023',
                'color' => 'White',
                'category' => 'luxury',
                'daily_rate' => 1299.00,
                'weekly_rate' => 8000.00,
                'monthly_rate' => 30000.00,
                'transmission' => 'Automatic',
                'seats' => 5,
                'doors' => 4,
                'odometer' => 15000,
                'description' => 'Experience the ultimate in luxury and performance with the BMW 7 Series 740i. Perfect for business, leisure, and special occasions in the UAE.',
                'image' => null
            ],
            [
                'api_id' => '2',
                'plate_number' => 'CC-30531',
                'status' => 'available',
                'ownership_status' => 'owned',
                'make' => 'Mercedes Benz',
                'model' => 'S500',
                'year' => '2022',
                'color' => 'White',
                'category' => 'luxury',
                'daily_rate' => 1199.00,
                'weekly_rate' => 7500.00,
                'monthly_rate' => 28000.00,
                'transmission' => 'Automatic',
                'seats' => 5,
                'doors' => 4,
                'odometer' => 25000,
                'description' => 'Experience the ultimate in luxury and performance with the Mercedes Benz S500. Perfect for business, leisure, and special occasions in the UAE.',
                'image' => null
            ],
            [
                'api_id' => '3',
                'plate_number' => 'CC-92505',
                'status' => 'available',
                'ownership_status' => 'owned',
                'make' => 'BMW',
                'model' => '3 Series 330i',
                'year' => '2024',
                'color' => 'Silver',
                'category' => 'mid-range',
                'daily_rate' => 399.00,
                'weekly_rate' => 2500.00,
                'monthly_rate' => 9500.00,
                'transmission' => 'Automatic',
                'seats' => 5,
                'doors' => 4,
                'odometer' => 5000,
                'description' => 'Enjoy comfort and style with the BMW 3 Series 330i. Ideal for both business and leisure travel in the UAE.',
                'image' => null
            ],
            [
                'api_id' => '4',
                'plate_number' => 'A-48785',
                'status' => 'available',
                'ownership_status' => 'owned',
                'make' => 'Nissan',
                'model' => 'Versa',
                'year' => '2021',
                'color' => 'White',
                'category' => 'economy',
                'daily_rate' => 79.00,
                'weekly_rate' => 500.00,
                'monthly_rate' => 1800.00,
                'transmission' => 'Automatic',
                'seats' => 5,
                'doors' => 4,
                'odometer' => 45000,
                'description' => 'Reliable and efficient, the Nissan Versa offers great value for your daily transportation needs in the UAE.',
                'image' => null
            ],
            [
                'api_id' => '5',
                'plate_number' => 'CC-12345',
                'status' => 'rented',
                'ownership_status' => 'owned',
                'make' => 'Toyota',
                'model' => 'Land Cruiser',
                'year' => '2023',
                'color' => 'Black',
                'category' => 'suv',
                'daily_rate' => 899.00,
                'weekly_rate' => 5500.00,
                'monthly_rate' => 20000.00,
                'transmission' => 'Automatic',
                'seats' => 7,
                'doors' => 5,
                'odometer' => 12000,
                'description' => 'Powerful and spacious, the Toyota Land Cruiser is perfect for family trips and off-road adventures in the UAE.',
                'image' => null
            ],
            [
                'api_id' => '6',
                'plate_number' => 'CC-67890',
                'status' => 'maintenance',
                'ownership_status' => 'owned',
                'make' => 'Porsche',
                'model' => '911 Carrera',
                'year' => '2023',
                'color' => 'Red',
                'category' => 'sports',
                'daily_rate' => 1599.00,
                'weekly_rate' => 10000.00,
                'monthly_rate' => 38000.00,
                'transmission' => 'Automatic',
                'seats' => 2,
                'doors' => 2,
                'odometer' => 8000,
                'description' => 'Experience pure driving pleasure with the Porsche 911 Carrera. The ultimate sports car for thrill-seekers in the UAE.',
                'image' => null
            ]
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::create($vehicleData);
        }
    }
}
