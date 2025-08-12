<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BookingController;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TestBookingCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booking-creation {user_id} {vehicle_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the new booking creation method that creates bookings in both systems';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $vehicleId = $this->argument('vehicle_id');

        $this->info("Testing booking creation for User ID: {$userId}, Vehicle ID: {$vehicleId}");

        // Check if user and vehicle exist
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return;
        }

        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle) {
            $this->error("Vehicle with ID {$vehicleId} not found!");
            return;
        }

        $this->info("✓ User found: {$user->name} ({$user->email})");
        $this->info("✓ Vehicle found: {$vehicle->make} {$vehicle->model} ({$vehicle->plate_number})");

        // Check if user has external_customer_id
        if (!$user->external_customer_id) {
            $this->warn("⚠ User does not have external_customer_id. This might cause issues with external booking.");
        } else {
            $this->info("✓ User has external_customer_id: {$user->external_customer_id}");
        }

        // Check if vehicle has api_id
        if (!$vehicle->api_id) {
            $this->warn("⚠ Vehicle does not have api_id. This might cause issues with external booking.");
        } else {
            $this->info("✓ Vehicle has api_id: {$vehicle->api_id}");
        }

        // Check vehicle availability
        if (strtolower($vehicle->status) !== 'available') {
            $this->warn("⚠ Vehicle is not available. Status: {$vehicle->status}");
        } else {
            $this->info("✓ Vehicle is available");
        }

        // Create test booking data
        $startDate = now()->addDays(2)->format('Y-m-d');
        $endDate = now()->addDays(5)->format('Y-m-d');
        $totalDays = 3;
        $totalAmount = $vehicle->daily_rate * $totalDays;

        $bookingData = [
            'vehicle_id' => $vehicleId,
            'user_id' => $userId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'total_amount' => $totalAmount,
            'pricing_type' => 'daily',
            'applied_rate' => $vehicle->daily_rate,
            'emirate' => 'Dubai',
            'notes' => 'Test booking created via command line',
            'pickup_location' => 'Dubai Airport',
            'dropoff_location' => 'Dubai Mall'
        ];

        $this->info("\nBooking Data:");
        $this->table(
            ['Field', 'Value'],
            [
                ['Start Date', $startDate],
                ['End Date', $endDate],
                ['Total Days', $totalDays],
                ['Total Amount', $totalAmount],
                ['Pricing Type', 'daily'],
                ['Applied Rate', $vehicle->daily_rate],
                ['Emirate', 'Dubai']
            ]
        );

        // Ask for confirmation
        if (!$this->confirm('Do you want to proceed with creating this booking?')) {
            $this->info('Booking creation cancelled.');
            return;
        }

        // Create booking using the controller method
        $controller = new BookingController(app(\App\Services\ExternalBookingService::class));
        $request = new Request($bookingData);

        try {
            $this->info("\nCreating booking in both systems...");
            $response = $controller->createBookingInBothSystems($request);
            $responseData = $response->getData(true);

            if ($responseData['success']) {
                $this->info("✓ Booking created successfully!");
                
                $this->info("\nLocal Booking Details:");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['ID', $responseData['data']['local_booking']['id']],
                        ['Status', $responseData['data']['local_booking']['status']],
                        ['Total Amount', $responseData['data']['local_booking']['total_amount']],
                        ['Start Date', $responseData['data']['local_booking']['start_date']],
                        ['End Date', $responseData['data']['local_booking']['end_date']]
                    ]
                );

                $this->info("\nExternal Booking Details:");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Reservation ID', $responseData['data']['external_booking']['reservation_id'] ?? 'N/A'],
                        ['Status', $responseData['data']['external_booking']['status']]
                    ]
                );

                $this->info("\nVehicle Status Updated:");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['ID', $responseData['data']['vehicle']['id']],
                        ['Make/Model', $responseData['data']['vehicle']['make'] . ' ' . $responseData['data']['vehicle']['model']],
                        ['New Status', $responseData['data']['vehicle']['status']]
                    ]
                );

            } else {
                $this->error("✗ Booking creation failed!");
                $this->error("Message: " . $responseData['message']);
                
                if (isset($responseData['external_error'])) {
                    $this->error("External Error: " . $responseData['external_error']);
                }
                
                if (isset($responseData['error'])) {
                    $this->error("Error: " . $responseData['error']);
                }
            }

        } catch (\Exception $e) {
            $this->error("✗ Exception occurred: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }

        $this->info("\nTest completed!");
    }
} 