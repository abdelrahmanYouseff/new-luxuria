<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class VehiclesApiController extends Controller
{
    private $apiUrl = 'https://rlapp.rentluxuria.com/api/vehicles';
    private $apiKey = '28izx09iasdasd';

    public function index()
    {
        try {
            // First, sync vehicles from API to database
            $syncResult = $this->syncVehiclesToDatabase();

            // Then get vehicles from database
            $vehicles = Vehicle::orderBy('daily_rate', 'desc')->get();

            return Inertia::render('VehiclesApi', [
                'vehicles' => $vehicles,
                'apiStatus' => $syncResult['success'] ? 'success' : 'error',
                'error' => $syncResult['success'] ? null : $syncResult['error'],
                'totalCount' => $vehicles->count(),
                'syncResult' => $syncResult
            ]);
        } catch (\Exception $e) {
            // Fallback to database data on exception
            $vehicles = Vehicle::orderBy('daily_rate', 'desc')->get();

            return Inertia::render('VehiclesApi', [
                'vehicles' => $vehicles,
                'apiStatus' => 'error',
                'error' => 'API connection failed. Showing database data.',
                'totalCount' => $vehicles->count()
            ]);
        }
    }

    /**
     * Sync vehicles from API to database
     */
    private function syncVehiclesToDatabase()
    {
        try {
            $response = Http::withHeaders([
                'X-RLAPP-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->apiUrl);

            if ($response->successful()) {
                $responseData = $response->json();
                $apiVehicles = $responseData['data'] ?? [];

                $syncedCount = 0;
                $updatedCount = 0;
                $errors = [];

                foreach ($apiVehicles as $vehicleData) {
                    try {
                        $result = $this->syncVehicleToDatabase($vehicleData);
                        if ($result['action'] === 'created') {
                            $syncedCount++;
                        } elseif ($result['action'] === 'updated') {
                            $updatedCount++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = [
                            'api_id' => $vehicleData['id'] ?? 'unknown',
                            'error' => $e->getMessage()
                        ];
                    }
                }

                return [
                    'success' => true,
                    'total_api_vehicles' => count($apiVehicles),
                    'synced_count' => $syncedCount,
                    'updated_count' => $updatedCount,
                    'errors' => $errors
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'API request failed',
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'API connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync single vehicle from API to database
     */
    private function syncVehicleToDatabase($vehicleData)
    {
        $apiId = $vehicleData['id'] ?? null;
        $vehicleInfo = $vehicleData['vehicle_info'] ?? [];
        $pricing = $vehicleData['pricing'] ?? [];

        // Find existing vehicle by API ID
        $vehicle = Vehicle::where('api_id', $apiId)->first();

        $vehicleDataToSave = [
            'api_id' => $apiId,
            'plate_number' => $vehicleData['plate_number'] ?? null,
            'status' => $this->mapStatus($vehicleData['status'] ?? 'available'),
            'ownership_status' => $this->mapOwnership($vehicleData['ownership_status'] ?? 'owned'),
            'make' => $vehicleInfo['make'] ?? null,
            'model' => $vehicleInfo['model'] ?? null,
            'year' => $vehicleInfo['year'] ?? null,
            'color' => $vehicleInfo['color'] ?? null,
            'category' => $this->mapCategory($vehicleInfo['category'] ?? 'economy'),
            'daily_rate' => (float) ($pricing['daily'] ?? 0),
            'weekly_rate' => (float) ($pricing['weekly'] ?? 0),
            'monthly_rate' => (float) ($pricing['monthly'] ?? 0),
            'transmission' => 'Automatic',
            'seats' => $this->getSeatsByCategory($this->mapCategory($vehicleInfo['category'] ?? 'economy')),
            'doors' => 4,
            'odometer' => 0,
            'description' => $this->generateDescription($vehicleInfo['make'] ?? '', $vehicleInfo['model'] ?? '', $vehicleInfo['category'] ?? '')
        ];

        if ($vehicle) {
            // Update existing vehicle
            $vehicle->update($vehicleDataToSave);
            return ['action' => 'updated', 'vehicle' => $vehicle];
        } else {
            // Create new vehicle
            $vehicle = Vehicle::create($vehicleDataToSave);
            return ['action' => 'created', 'vehicle' => $vehicle];
        }
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
            'van' => 'Van',
            'truck' => 'Truck',
            'mid-range/ premium' => 'Mid-Range',
            'mid-range' => 'Mid-Range',
            'premium' => 'Premium'
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

    private function getMockVehicles()
    {
        return [
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
                'image' => null
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
                'image' => null
            ],
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
                'image' => null
            ],
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
                'image' => null
            ]
        ];
    }

    /**
     * Get seats by category
     */
    private function getSeatsByCategory($category)
    {
        $seatsMap = [
            'luxury' => 5,
            'mid-range' => 5,
            'economy' => 5,
            'suv' => 7,
            'sports' => 2
        ];

        return $seatsMap[$category] ?? 5;
    }

    /**
     * Generate description for vehicle
     */
    private function generateDescription($make, $model, $category)
    {
        $category = strtolower($category);
        if (strpos($category, 'luxury') !== false) {
            return "Experience the ultimate in luxury and performance with the {$make} {$model}. Perfect for business, leisure, and special occasions in the UAE.";
        } elseif (strpos($category, 'mid-range') !== false || strpos($category, 'premium') !== false) {
            return "Enjoy comfort and style with the {$make} {$model}. Ideal for both business and leisure travel in the UAE.";
        } else {
            return "Reliable and efficient, the {$make} {$model} offers great value for your daily transportation needs in the UAE.";
        }
    }
}
