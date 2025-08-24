<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class VehiclesApiController extends Controller
{
    private $apiUrl = 'https://rlapp.rentluxuria.com/api/vehicles';
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.rlapp.api_key');
    }

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
                'syncResult' => $syncResult,
                'apiKey' => $this->apiKey
            ]);
        } catch (\Exception $e) {
            // Fallback to database data on exception
            $vehicles = Vehicle::orderBy('daily_rate', 'desc')->get();

            return Inertia::render('VehiclesApi', [
                'vehicles' => $vehicles,
                'apiStatus' => 'error',
                'error' => 'API connection failed. Showing database data.',
                'totalCount' => $vehicles->count(),
                'apiKey' => $this->apiKey
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
                'X-API-KEY' => $this->apiKey,
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
                    'response_body' => $response->body(),
                    'url' => $this->apiUrl,
                    'api_key' => $this->apiKey,
                    'headers_sent' => [
                        'X-API-KEY' => $this->apiKey,
                        'Accept' => 'application/json'
                    ]
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
     * API endpoint to get all vehicles with filtering and pagination
     */
    public function getVehicles(Request $request)
    {
        try {
            $query = Vehicle::query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', 'like', '%' . $request->status . '%');
            }

            // Filter by category
            if ($request->has('category')) {
                $query->byCategory($request->category);
            }

            // Filter by make
            if ($request->has('make')) {
                $query->byMake($request->make);
            }

            // Filter by availability
            if ($request->has('available') && $request->available == 'true') {
                $query->available();
            }

            // Filter by visibility
            if ($request->has('visible')) {
                if ($request->visible == 'true') {
                    $query->visible();
                } else {
                    $query->hidden();
                }
            } else {
                // Default to visible only
                $query->visible();
            }

            // Price range filtering
            if ($request->has('min_price')) {
                $query->where('daily_rate', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('daily_rate', '<=', $request->max_price);
            }

            // Search by name (make + model)
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('make', 'like', '%' . $search . '%')
                      ->orWhere('model', 'like', '%' . $search . '%')
                      ->orWhere('plate_number', 'like', '%' . $search . '%');
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'daily_rate');
            $sortDirection = $request->get('sort_direction', 'asc');

            if (in_array($sortBy, ['daily_rate', 'make', 'model', 'year', 'category', 'status'])) {
                $query->orderBy($sortBy, $sortDirection);
            }

            // Pagination
            $perPage = min($request->get('per_page', 10), 100); // Max 100 items per page
            $vehicles = $query->paginate($perPage);

            // Transform vehicles to include image_url
            $vehiclesData = $vehicles->items();
            $transformedData = collect($vehiclesData)->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'api_id' => $vehicle->api_id,
                    'plate_number' => $vehicle->plate_number,
                    'status' => $vehicle->status,
                    'ownership_status' => $vehicle->ownership_status,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'year' => $vehicle->year,
                    'color' => $vehicle->color,
                    'category' => $vehicle->category,
                    'daily_rate' => $vehicle->daily_rate,
                    'weekly_rate' => $vehicle->weekly_rate,
                    'monthly_rate' => $vehicle->monthly_rate,
                    'transmission' => $vehicle->transmission,
                    'seats' => $vehicle->seats,
                    'doors' => $vehicle->doors,
                    'odometer' => $vehicle->odometer,
                    'description' => $vehicle->description,
                    'image' => $vehicle->image,
                    'image_url' => $vehicle->image_url,
                    'is_visible' => $vehicle->is_visible,
                    'created_at' => $vehicle->created_at,
                    'updated_at' => $vehicle->updated_at
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'pagination' => [
                    'current_page' => $vehicles->currentPage(),
                    'last_page' => $vehicles->lastPage(),
                    'per_page' => $vehicles->perPage(),
                    'total' => $vehicles->total(),
                    'from' => $vehicles->firstItem(),
                    'to' => $vehicles->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get a specific vehicle by ID
     */
    public function getVehicle($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            // Transform vehicle to include image_url
            $vehicleData = [
                'id' => $vehicle->id,
                'api_id' => $vehicle->api_id,
                'plate_number' => $vehicle->plate_number,
                'status' => $vehicle->status,
                'ownership_status' => $vehicle->ownership_status,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'year' => $vehicle->year,
                'color' => $vehicle->color,
                'category' => $vehicle->category,
                'daily_rate' => $vehicle->daily_rate,
                'weekly_rate' => $vehicle->weekly_rate,
                'monthly_rate' => $vehicle->monthly_rate,
                'transmission' => $vehicle->transmission,
                'seats' => $vehicle->seats,
                'doors' => $vehicle->doors,
                'odometer' => $vehicle->odometer,
                'description' => $vehicle->description,
                'image' => $vehicle->image,
                'image_url' => $vehicle->image_url,
                'is_visible' => $vehicle->is_visible,
                'created_at' => $vehicle->created_at,
                'updated_at' => $vehicle->updated_at
            ];

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * API endpoint to get available vehicles only
     */
    public function getAvailableVehicles(Request $request)
    {
        try {
            $query = Vehicle::available()->visible();

            // Apply same filters as getVehicles but only for available vehicles
            if ($request->has('category')) {
                $query->byCategory($request->category);
            }

            if ($request->has('make')) {
                $query->byMake($request->make);
            }

            if ($request->has('min_price')) {
                $query->where('daily_rate', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('daily_rate', '<=', $request->max_price);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('make', 'like', '%' . $search . '%')
                      ->orWhere('model', 'like', '%' . $search . '%');
                });
            }

            $sortBy = $request->get('sort_by', 'daily_rate');
            $sortDirection = $request->get('sort_direction', 'asc');

            if (in_array($sortBy, ['daily_rate', 'make', 'model', 'year', 'category'])) {
                $query->orderBy($sortBy, $sortDirection);
            }

            $vehicles = $query->get();

            // Transform vehicles to include image_url
            $transformedData = $vehicles->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'api_id' => $vehicle->api_id,
                    'plate_number' => $vehicle->plate_number,
                    'status' => $vehicle->status,
                    'ownership_status' => $vehicle->ownership_status,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'year' => $vehicle->year,
                    'color' => $vehicle->color,
                    'category' => $vehicle->category,
                    'daily_rate' => $vehicle->daily_rate,
                    'weekly_rate' => $vehicle->weekly_rate,
                    'monthly_rate' => $vehicle->monthly_rate,
                    'transmission' => $vehicle->transmission,
                    'seats' => $vehicle->seats,
                    'doors' => $vehicle->doors,
                    'odometer' => $vehicle->odometer,
                    'description' => $vehicle->description,
                    'image' => $vehicle->image,
                    'image_url' => $vehicle->image_url,
                    'is_visible' => $vehicle->is_visible,
                    'created_at' => $vehicle->created_at,
                    'updated_at' => $vehicle->updated_at
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'total' => count($transformedData)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available vehicles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get vehicle categories
     */
    public function getCategories()
    {
        try {
            $categories = Vehicle::select('category')
                                ->distinct()
                                ->whereNotNull('category')
                                ->pluck('category');

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get vehicle makes
     */
    public function getMakes()
    {
        try {
            $makes = Vehicle::select('make')
                           ->distinct()
                           ->whereNotNull('make')
                           ->pluck('make');

            return response()->json([
                'success' => true,
                'data' => $makes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve makes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to sync vehicles from external API
     */
    public function syncVehicles()
    {
        try {
            $syncResult = $this->syncVehiclesToDatabase();

            return response()->json([
                'success' => $syncResult['success'],
                'message' => $syncResult['success'] ? 'Vehicles synced successfully' : 'Failed to sync vehicles',
                'data' => $syncResult
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync vehicles',
                'error' => $e->getMessage()
            ], 500);
        }
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

    /**
     * Test API connection endpoint
     */
    public function testApiConnection()
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->apiUrl);

            $result = [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'url' => $this->apiUrl,
                'api_key' => $this->apiKey,
                'response_body' => $response->body(),
                'response_size' => strlen($response->body()),
                'timestamp' => now()->toISOString()
            ];

            if ($response->successful()) {
                $responseData = $response->json();
                $result['parsed_data'] = $responseData;
                $result['vehicles_count'] = count($responseData['data'] ?? []);
                $result['message'] = 'API connection successful';
            } else {
                $result['message'] = 'API connection failed';
                $result['error'] = 'HTTP ' . $response->status() . ' - ' . $response->reasonPhrase();
            }

            return response()->json($result);

        } catch (\Exception $e) {
            $errorResult = [
                'success' => false,
                'message' => 'API connection test failed',
                'error' => $e->getMessage(),
                'exception_type' => get_class($e),
                'url' => $this->apiUrl,
                'api_key' => $this->apiKey,
                'timestamp' => now()->toISOString()
            ];

            return response()->json($errorResult, 500);
        }
    }

    /**
     * API endpoint to check vehicle status
     */
    public function getVehicleStatus($id)
    {
        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle not found'
                ], 404);
            }

            // Check if vehicle is available for booking
            $isAvailable = strtolower($vehicle->status) === 'available';

            // Get additional status information
            $statusInfo = [
                'status' => $vehicle->status,
                'is_available' => $isAvailable,
                'can_be_booked' => $isAvailable && $vehicle->is_visible,
                'ownership_status' => $vehicle->ownership_status,
                'last_updated' => $vehicle->updated_at
            ];

            // Add status-specific details
            switch (strtolower($vehicle->status)) {
                case 'available':
                    $statusInfo['message'] = 'Vehicle is available for booking';
                    $statusInfo['status_code'] = 'AVAILABLE';
                    break;
                case 'rented':
                    $statusInfo['message'] = 'Vehicle is currently rented out';
                    $statusInfo['status_code'] = 'RENTED';
                    break;
                case 'maintenance':
                    $statusInfo['message'] = 'Vehicle is under maintenance';
                    $statusInfo['status_code'] = 'MAINTENANCE';
                    break;
                case 'out_of_service':
                    $statusInfo['message'] = 'Vehicle is out of service';
                    $statusInfo['status_code'] = 'OUT_OF_SERVICE';
                    break;
                case 'reserved':
                    $statusInfo['message'] = 'Vehicle is reserved for another customer';
                    $statusInfo['status_code'] = 'RESERVED';
                    break;
                default:
                    $statusInfo['message'] = 'Vehicle status is unknown';
                    $statusInfo['status_code'] = 'UNKNOWN';
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'vehicle_id' => $vehicle->id,
                    'api_id' => $vehicle->api_id,
                    'plate_number' => $vehicle->plate_number,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'year' => $vehicle->year,
                    'status_info' => $statusInfo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
