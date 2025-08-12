<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VehicleApiService
{
    private $apiUrl = 'https://rlapp.rentluxuria.com/api/vehicles';
    private $apiKey = '[REDACTED_RLAPP_API_KEY]';

    /**
     * Sync vehicles from API to database
     */
    public function syncVehicles()
    {
        try {
            $response = Http::withHeaders([
                'X-RLAPP-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->apiUrl);

            if ($response->successful()) {
                $responseData = $response->json();
                $vehicles = $responseData['data'] ?? [];

                $syncedCount = 0;
                $updatedCount = 0;
                $errors = [];

                foreach ($vehicles as $vehicleData) {
                    try {
                        $result = $this->syncVehicle($vehicleData);
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
                        Log::error('Vehicle sync error', [
                            'api_id' => $vehicleData['id'] ?? 'unknown',
                            'error' => $e->getMessage(),
                            'data' => $vehicleData
                        ]);
                    }
                }

                return [
                    'success' => true,
                    'total_api_vehicles' => count($vehicles),
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
            Log::error('Vehicle API sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'API connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync single vehicle from API data
     */
    public function syncVehicle($vehicleData)
    {
        $apiId = $vehicleData['id'] ?? null;
        $vehicleInfo = $vehicleData['vehicle_info'] ?? [];
        $pricing = $vehicleData['pricing'] ?? [];

        // Find existing vehicle by API ID
        $vehicle = Vehicle::where('api_id', $apiId)->first();

        $vehicleData = [
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
            $vehicle->update($vehicleData);
            return ['action' => 'updated', 'vehicle' => $vehicle];
        } else {
            // Create new vehicle
            $vehicle = Vehicle::create($vehicleData);
            return ['action' => 'created', 'vehicle' => $vehicle];
        }
    }

    /**
     * Get vehicles from database with optional API sync
     */
    public function getVehicles($syncFromApi = false)
    {
        if ($syncFromApi) {
            $this->syncVehicles();
        }

        return Vehicle::orderBy('daily_rate', 'desc')->get();
    }

    /**
     * Get vehicles by category
     */
    public function getVehiclesByCategory($category)
    {
        return Vehicle::byCategory($category)->orderBy('daily_rate', 'desc')->get();
    }

    /**
     * Get available vehicles
     */
    public function getAvailableVehicles()
    {
        return Vehicle::available()->orderBy('daily_rate', 'desc')->get();
    }

    /**
     * Map API status to database status
     */
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

    /**
     * Map API category to database category
     */
    private function mapCategory($category)
    {
        $categoryMap = [
            'economy' => 'economy',
            'luxury' => 'luxury',
            'suv' => 'suv',
            'sports' => 'sports',
            'van' => 'suv',
            'truck' => 'suv',
            'mid-range/ premium' => 'mid-range',
            'mid-range' => 'mid-range',
            'premium' => 'mid-range'
        ];

        return $categoryMap[strtolower($category)] ?? 'economy';
    }

    /**
     * Map API ownership to database ownership
     */
    private function mapOwnership($ownership)
    {
        $ownershipMap = [
            'owned' => 'owned',
            'leased' => 'leased',
            'rented' => 'rented'
        ];

        return $ownershipMap[strtolower($ownership)] ?? 'owned';
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
