<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Models\Vehicle;

class HomeController extends Controller
{
    private $apiUrl = 'https://rlapp.rentluxuria.com/api/vehicles';
    private $apiKey = '28izx09iasdasd';

    public function index()
    {
        try {
            // Get vehicles from local database, ordered by daily rate (highest to lowest)
            $vehicles = Vehicle::orderBy('daily_rate', 'desc')->get();

            if ($vehicles->count() > 0) {
                // Transform and categorize vehicles from database
                $categorizedVehicles = $this->categorizeVehiclesFromDatabase($vehicles);

                return view('home', [
                    'categories' => $categorizedVehicles,
                    'dataSource' => 'database',
                    'totalVehicles' => $vehicles->count()
                ]);
            } else {
                // Return empty categories if no vehicles in database
                return view('home', [
                    'categories' => [],
                    'dataSource' => 'database',
                    'totalVehicles' => 0
                ]);
            }
        } catch (\Exception $e) {
            // Return empty categories on exception
            return view('home', [
                'categories' => [],
                'dataSource' => 'database',
                'totalVehicles' => 0
            ]);
        }
    }

    private function categorizeVehiclesFromDatabase($vehicles)
    {
        $categories = [
            'Luxury' => [],
            'Mid-Range' => [],
            'Economy' => [],
            'Sports' => [],
            'SUV' => [],
            'Vans' => []
        ];

        // Show only one representative card per make+model (grouped), regardless of number of plates
        $grouped = $vehicles->groupBy(function($v){
            return strtolower(trim(($v->make ?? '') . '|' . ($v->model ?? '')));
        });

        foreach ($grouped as $group) {
            // Prefer an Available unit to represent the model, otherwise take the first
            $vehicle = $group->first(function($v){ return strtolower($v->status) === 'available'; }) ?? $group->first();
            $category = $this->mapCategory($vehicle->category ?? 'economy');

            $transformedVehicle = [
                'id' => $vehicle->id,
                'name' => $vehicle->make ?? 'Unknown',
                'model' => $vehicle->model ?? 'N/A',
                'plateNumber' => $vehicle->plate_number ?? 'N/A',
                'status' => $this->mapStatus($vehicle->status ?? 'available'),
                'category' => $category,
                'ownership' => $this->mapOwnership($vehicle->ownership_status ?? 'owned'),
                'year' => $vehicle->year ?? 'N/A',
                'color' => $vehicle->color ?? 'N/A',
                'transmission' => $vehicle->transmission ?? 'Automatic',
                'odometer' => $vehicle->odometer ?? 0,
                'dailyRate' => (float) ($vehicle->daily_rate ?? 0),
                'weeklyRate' => (float) ($vehicle->weekly_rate ?? 0),
                'monthlyRate' => (float) ($vehicle->monthly_rate ?? 0),
                'image' => $vehicle->image_url, // Use the image_url accessor
                'seats' => $this->getSeatsByCategory($category),
                'doors' => $vehicle->doors ?? 4,
                'deposit' => $vehicle->deposit ?? 'No Deposit'
            ];

            // Add to appropriate category
            if (isset($categories[$category])) {
                $categories[$category][] = $transformedVehicle;
            } else {
                $categories['Economy'][] = $transformedVehicle; // Default fallback
            }
        }

        // Remove empty categories
        return array_filter($categories, function($vehicles) {
            return count($vehicles) > 0;
        });
    }

    private function mapStatus($status)
    {
        $statusMap = [
            'available' => 'Available',
            'rented' => 'Rented',
            'maintenance' => 'Maintenance',
            'out_of_service' => 'Out of Service',
            'reserved' => 'Reserved'
        ];

        return $statusMap[strtolower($status)] ?? 'Available';
    }

    private function mapCategory($category)
    {
        $categoryMap = [
            'economy' => 'Economy',
            'luxury' => 'Luxury',
            'suv' => 'SUV',
            'sports' => 'Sports',
            'van' => 'Vans',
            'vans' => 'Vans',
            'bus' => 'Vans',
            'truck' => 'Vans',
            'mid-range/ premium' => 'Mid-Range',
            'mid-range' => 'Mid-Range',
            'premium' => 'Mid-Range'
        ];

        return $categoryMap[strtolower($category)] ?? 'Economy';
    }

    private function mapOwnership($ownership)
    {
        $ownershipMap = [
            'owned' => 'Owned',
            'leased' => 'Leased',
            'rented' => 'Rented'
        ];

        return $ownershipMap[strtolower($ownership)] ?? 'Owned';
    }

    private function getSeatsByCategory($category)
    {
        $seatsMap = [
            'Luxury' => 5,
            'Mid-Range' => 5,
            'Economy' => 5,
            'SUV' => 7
        ];

        return $seatsMap[$category] ?? 5;
    }

    private function getMockCategories()
    {
        return [
            'Luxury' => [
                [
                    'id' => 1,
                    'name' => 'BMW',
                    'model' => '7 Series 740i',
                    'plateNumber' => 'CC-51054',
                    'status' => 'Available',
                    'category' => 'Luxury',
                    'ownership' => 'Owned',
                    'year' => '2023',
                    'color' => 'White',
                    'transmission' => 'Automatic',
                    'odometer' => 0,
                    'dailyRate' => 1299,
                    'weeklyRate' => 7699,
                    'monthlyRate' => 24500,
                    'image' => null,
                    'seats' => 5,
                    'doors' => 4,
                    'deposit' => 'No Deposit'
                ],
                [
                    'id' => 2,
                    'name' => 'Mercedes Benz',
                    'model' => 'S500',
                    'plateNumber' => 'CC-30531',
                    'status' => 'Available',
                    'category' => 'Luxury',
                    'ownership' => 'Owned',
                    'year' => '2022',
                    'color' => 'White',
                    'transmission' => 'Automatic',
                    'odometer' => 0,
                    'dailyRate' => 1199,
                    'weeklyRate' => 7199,
                    'monthlyRate' => 22999,
                    'image' => null,
                    'seats' => 5,
                    'doors' => 4,
                    'deposit' => 'No Deposit'
                ]
            ],
            'Mid-Range' => [
                [
                    'id' => 3,
                    'name' => 'BMW',
                    'model' => '3 Series 330i',
                    'plateNumber' => 'CC-92505',
                    'status' => 'Available',
                    'category' => 'Mid-Range',
                    'ownership' => 'Owned',
                    'year' => '2024',
                    'color' => 'Silver',
                    'transmission' => 'Automatic',
                    'odometer' => 0,
                    'dailyRate' => 399,
                    'weeklyRate' => 2999,
                    'monthlyRate' => 7999,
                    'image' => null,
                    'seats' => 5,
                    'doors' => 4,
                    'deposit' => 'No Deposit'
                ]
            ],
            'Economy' => [
                [
                    'id' => 4,
                    'name' => 'Nissan',
                    'model' => 'Versa',
                    'plateNumber' => 'A-48785',
                    'status' => 'Available',
                    'category' => 'Economy',
                    'ownership' => 'Owned',
                    'year' => '2021',
                    'color' => 'White',
                    'transmission' => 'Automatic',
                    'odometer' => 0,
                    'dailyRate' => 79,
                    'weeklyRate' => 469,
                    'monthlyRate' => 1699,
                    'image' => null,
                    'seats' => 5,
                    'doors' => 4,
                    'deposit' => 'No Deposit'
                ]
            ]
        ];
    }
}
