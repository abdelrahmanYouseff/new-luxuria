<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    private $apiUrl = 'https://rlapp.rentluxuria.com/api/vehicles';
    private $apiKey = '[REDACTED_RLAPP_API_KEY]';

    public function index()
    {
        try {
            // Get all vehicles from database (both visible and hidden), ordered by daily rate (highest to lowest)
            $vehicles = Vehicle::orderBy('daily_rate', 'desc')->get();

            // Transform database data to match frontend structure
            $transformedVehicles = $vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->make ?? 'Unknown',
                    'model' => $vehicle->model ?? 'N/A',
                    'plateNumber' => $vehicle->plate_number ?? 'N/A',
                    'status' => $vehicle->status,
                    'category' => $vehicle->category,
                    'ownership' => $vehicle->ownership_status,
                    'year' => $vehicle->year ?? 'N/A',
                    'color' => $vehicle->color ?? 'N/A',
                    'transmission' => $vehicle->transmission,
                    'odometer' => $vehicle->odometer,
                    'dailyRate' => (float) $vehicle->daily_rate,
                    'image' => $vehicle->image_url,
                    'image_url' => $vehicle->image_url,
                    'is_visible' => $vehicle->is_visible,
                ];
            })->toArray();

            return Inertia::render('Vehicles', [
                'vehicles' => $transformedVehicles,
                'apiStatus' => 'success',
                'totalCount' => count($transformedVehicles),
                'dataSource' => 'database'
            ]);
        } catch (\Exception $e) {
            // Fallback to mock data on exception
            $mockVehicles = $this->getMockVehicles();

            return Inertia::render('Vehicles', [
                'vehicles' => $mockVehicles,
                'apiStatus' => 'error',
                'error' => 'Database connection failed. Showing mock data.',
                'totalCount' => count($mockVehicles),
                'dataSource' => 'mock'
            ]);
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
                'name' => 'Toyota Camry',
                'model' => '2023',
                'plateNumber' => 'ABC-123',
                'status' => 'Available',
                'category' => 'Economy',
                'ownership' => 'Owned',
                'year' => '2023',
                'color' => 'White',
                'transmission' => 'Automatic',
                'odometer' => 15000,
                'dailyRate' => 150,
                'image' => null
            ],
            [
                'id' => 2,
                'name' => 'BMW X5',
                'model' => '2022',
                'plateNumber' => 'XYZ-789',
                'status' => 'Rented',
                'category' => 'Luxury',
                'ownership' => 'Owned',
                'year' => '2022',
                'color' => 'Black',
                'transmission' => 'Automatic',
                'odometer' => 25000,
                'dailyRate' => 350,
                'image' => null
            ],
            [
                'id' => 3,
                'name' => 'Honda CR-V',
                'model' => '2023',
                'plateNumber' => 'DEF-456',
                'status' => 'Maintenance',
                'category' => 'SUV',
                'ownership' => 'Leased',
                'year' => '2023',
                'color' => 'Silver',
                'transmission' => 'Automatic',
                'odometer' => 18000,
                'dailyRate' => 200,
                'image' => null
            ],
            [
                'id' => 4,
                'name' => 'Mercedes C-Class',
                'model' => '2023',
                'plateNumber' => 'GHI-789',
                'status' => 'Available',
                'category' => 'Luxury',
                'ownership' => 'Owned',
                'year' => '2023',
                'color' => 'Blue',
                'transmission' => 'Automatic',
                'odometer' => 12000,
                'dailyRate' => 300,
                'image' => null
            ],
            [
                'id' => 5,
                'name' => 'Nissan Altima',
                'model' => '2022',
                'plateNumber' => 'JKL-012',
                'status' => 'Available',
                'category' => 'Economy',
                'ownership' => 'Owned',
                'year' => '2022',
                'color' => 'Red',
                'transmission' => 'Automatic',
                'odometer' => 22000,
                'dailyRate' => 120,
                'image' => null
            ]
        ];
    }

    public function testApi()
    {
        try {
            $response = Http::withHeaders([
                'X-RLAPP-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->apiUrl);

            if ($response->successful()) {
                $responseData = $response->json();
                $vehicles = $responseData['data'] ?? [];

                // Show first 3 vehicles as sample
                $sampleVehicles = array_slice($vehicles, 0, 3);

                return response()->json([
                    'status' => 'success',
                    'statusCode' => $response->status(),
                    'totalVehicles' => count($vehicles),
                    'sampleVehicles' => $sampleVehicles,
                    'meta' => $responseData['meta'] ?? null,
                    'timestamp' => $responseData['timestamp'] ?? null
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'statusCode' => $response->status(),
                    'error' => $response->body(),
                    'data' => null
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get only visible vehicles for public website
     */
    public function publicIndex()
    {
        try {
            // Get only visible vehicles from database, ordered by daily rate (highest to lowest)
            $vehicles = Vehicle::visible()->orderBy('daily_rate', 'desc')->get();

            // Transform database data to match frontend structure
            $transformedVehicles = $vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->make ?? 'Unknown',
                    'model' => $vehicle->model ?? 'N/A',
                    'plateNumber' => $vehicle->plate_number ?? 'N/A',
                    'status' => $vehicle->status,
                    'category' => $vehicle->category,
                    'ownership' => $vehicle->ownership_status,
                    'year' => $vehicle->year ?? 'N/A',
                    'color' => $vehicle->color ?? 'N/A',
                    'transmission' => $vehicle->transmission,
                    'odometer' => $vehicle->odometer,
                    'dailyRate' => (float) $vehicle->daily_rate,
                    'image' => $vehicle->image_url,
                    'image_url' => $vehicle->image_url,
                    'is_visible' => $vehicle->is_visible,
                ];
            })->toArray();

            return Inertia::render('Vehicles', [
                'vehicles' => $transformedVehicles,
                'apiStatus' => 'success',
                'totalCount' => count($transformedVehicles),
                'dataSource' => 'database'
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Vehicles', [
                'vehicles' => [],
                'apiStatus' => 'error',
                'error' => 'Failed to load vehicles: ' . $e->getMessage(),
                'totalCount' => 0,
                'dataSource' => 'database'
            ]);
        }
    }

    /**
     * Toggle vehicle visibility
     */
    public function toggleVisibility(Vehicle $vehicle)
    {
        try {
            Log::info('Toggle visibility called for vehicle', [
                'vehicle_id' => $vehicle->id,
                'current_visibility' => $vehicle->is_visible,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'user_email' => \Illuminate\Support\Facades\Auth::user()?->email
            ]);

            $vehicle->update([
                'is_visible' => !$vehicle->is_visible
            ]);

            $vehicle->refresh();

            Log::info('Vehicle visibility updated successfully', [
                'vehicle_id' => $vehicle->id,
                'new_visibility' => $vehicle->is_visible
            ]);

            return response()->json([
                'success' => true,
                'message' => $vehicle->is_visible ? 'Vehicle is now visible' : 'Vehicle is now hidden',
                'is_visible' => $vehicle->is_visible
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling vehicle visibility', [
                'vehicle_id' => $vehicle->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating vehicle visibility: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync vehicles from API to database
     */
    public function syncFromApi()
    {
        try {
            $response = Http::withHeaders([
                'X-RLAPP-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->apiUrl);

            if ($response->successful()) {
                $responseData = $response->json();
                $apiVehicles = $responseData['data'] ?? [];

                Log::info('عدد السيارات القادمة من الـ API:', ['count' => count($apiVehicles)]);

                $syncedCount = 0;
                $updatedCount = 0;
                $errors = [];

                foreach ($apiVehicles as $vehicleData) {
                    try {
                        $result = $this->syncVehicleToDatabase($vehicleData);
                        if ($result['action'] === 'created') {
                            $syncedCount++;
                            Log::info('تم إضافة سيارة جديدة', ['api_id' => $vehicleData['id'] ?? null]);
                        } elseif ($result['action'] === 'updated') {
                            $updatedCount++;
                            Log::info('تم تحديث سيارة', ['api_id' => $vehicleData['id'] ?? null]);
                        }
                    } catch (\Exception $e) {
                        $errors[] = [
                            'api_id' => $vehicleData['id'] ?? 'unknown',
                            'error' => $e->getMessage()
                        ];
                        Log::error('خطأ أثناء إضافة/تحديث السيارة', [
                            'api_id' => $vehicleData['id'] ?? 'unknown',
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => "Sync completed! {$syncedCount} new vehicles added, {$updatedCount} vehicles updated.",
                    'total_api_vehicles' => count($apiVehicles),
                    'synced_count' => $syncedCount,
                    'updated_count' => $updatedCount,
                    'errors' => $errors
                ]);
            } else {
                Log::error('Vehicle API sync failed: API request failed', [
                    'status_code' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'API request failed',
                    'status_code' => $response->status(),
                    'body' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Vehicle API sync failed: Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'API connection failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
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
            'api_id' => $apiId, // فقط api_id وليس id
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
