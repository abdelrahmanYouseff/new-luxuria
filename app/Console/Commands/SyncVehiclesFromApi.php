<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncVehiclesFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicles:sync {--force : Force sync even if recent sync exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync vehicles data from external API to local database';

    /**
     * API configuration
     */
    private $apiUrl = 'https://rlapp.rentluxuria.com/api/vehicles';
    private $apiKey = '28izx09iasdasd';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚗 Starting vehicle sync from API...');

        try {
            // Make API request
            $response = Http::withHeaders([
                'X-RLAPP-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(30)->get($this->apiUrl);

            if (!$response->successful()) {
                $this->error("❌ API request failed with status: {$response->status()}");
                Log::error('Vehicle sync failed - API request error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return Command::FAILURE;
            }

            $responseData = $response->json();
            $apiVehicles = $responseData['data'] ?? [];

            if (empty($apiVehicles)) {
                $this->warn('⚠️  No vehicles received from API');
                return Command::SUCCESS;
            }

            $this->info("📥 Received " . count($apiVehicles) . " vehicles from API");

            // Process each vehicle
            $syncedCount = 0;
            $updatedCount = 0;
            $errors = [];

            $bar = $this->output->createProgressBar(count($apiVehicles));
            $bar->start();

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
                    Log::error('Vehicle sync error for vehicle', [
                        'api_id' => $vehicleData['id'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            // Display results
            $this->info("✅ Sync completed successfully!");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total API Vehicles', count($apiVehicles)],
                    ['New Vehicles Added', $syncedCount],
                    ['Vehicles Updated', $updatedCount],
                    ['Errors', count($errors)],
                ]
            );

            if (!empty($errors)) {
                $this->warn("⚠️  Some vehicles had errors:");
                foreach (array_slice($errors, 0, 5) as $error) {
                    $this->error("   - Vehicle {$error['api_id']}: {$error['error']}");
                }
                if (count($errors) > 5) {
                    $this->warn("   ... and " . (count($errors) - 5) . " more errors");
                }
            }

            // Log success
            Log::info('Vehicle sync completed successfully', [
                'total_vehicles' => count($apiVehicles),
                'synced_count' => $syncedCount,
                'updated_count' => $updatedCount,
                'errors_count' => count($errors)
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Sync failed: " . $e->getMessage());
            Log::error('Vehicle sync failed with exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
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
            'economy' => 'economy',
            'luxury' => 'luxury',
            'suv' => 'suv',
            'sports' => 'sports',
            'van' => 'van',
            'truck' => 'van',
            'mid-range/ premium' => 'mid-range',
            'mid-range' => 'mid-range',
            'premium' => 'mid-range'
        ];

        return $categoryMap[strtolower($category)] ?? 'economy';
    }

    private function mapOwnership($ownership)
    {
        $ownershipMap = [
            'owned' => 'owned',
            'leased' => 'leased',
            'rented' => 'rented'
        ];

        return $ownershipMap[strtolower($ownership)] ?? 'owned';
    }

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
