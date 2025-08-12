<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Booking;

class ExternalBookingService
{
    private string $apiUrl;
    private string $apiKey;
    private bool $useTest;
    private ExternalCustomerService $externalCustomerService;

    public function __construct(ExternalCustomerService $externalCustomerService)
    {
        $this->externalCustomerService = $externalCustomerService;
        $baseUrl = rtrim(config('services.rlapp.base_url', 'https://rlapp.rentluxuria.com'), '/');
        $this->useTest = (bool) config('services.rlapp.use_test', true);
        $prefix = $this->useTest ? '/api/v1/test' : '/api/v1';
        $this->apiUrl = $baseUrl . $prefix . '/custom-reservation';
        $this->apiKey = (string) config('services.rlapp.api_key', '');
    }

    /**
     * Create a booking in the external API
     */
    public function createExternalBooking($bookingData, $userId, $vehicleId)
    {
        try {
            // Get user and vehicle data
            $user = User::findOrFail($userId);
            $vehicle = Vehicle::findOrFail($vehicleId);

            // Decide the external customer id to use: prefer saved external_customer_id; otherwise create one with user's real email/phone
            $externalCustomerId = $user->external_customer_id;
            if (empty($externalCustomerId)) {
                try {
                    $customerPayload = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?? '0500000000',
                    ];
                    $createResult = $this->externalCustomerService->createExternalCustomer($customerPayload);
                    if (is_array($createResult) && ($createResult['success'] ?? false)) {
                        // Try to fetch id from response
                        $data = $createResult['response_data'] ?? [];
                        $externalCustomerId = $data['id'] ?? ($data['data']['id'] ?? null);
                        if ($externalCustomerId) {
                            $user->external_customer_id = $externalCustomerId;
                            $user->save();
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to create external customer; will fallback to pointsys id', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            if (empty($externalCustomerId)) {
                $externalCustomerId = $user->pointsys_customer_id ?? $this->generateCustomerId($user);
            }

            // Prepare the request data for external API
            $externalBookingData = [
                'customer_id' => $externalCustomerId,
                'vehicle_id' => $vehicle->api_id ?? $this->findVehicleByPlate($vehicle->plate_number),
                'pickup_date' => $this->formatDateTime($bookingData['start_date']),
                'pickup_location' => $this->mapEmirateToLocation($bookingData['emirate']),
                'return_date' => $this->formatDateTime($bookingData['end_date']),
                'rate' => (float) $bookingData['applied_rate'],
                'status' => 'pending',
                'notes' => $bookingData['notes'] ?? '',
                // extra customer context
                'customer_email' => $user->email,
                'customer_name' => $user->name,
                // some APIs might expect generic keys
                'email' => $user->email,
            ];

            // Make the API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->apiUrl, $externalBookingData);

            // Log the request and response for debugging
            Log::info('External Booking API Request', [
                'url' => $this->apiUrl,
                'data' => $externalBookingData,
                'response_status' => $response->status(),
                'response_body' => $response->body()
            ]);

                        if ($response->successful()) {
                $responseData = $response->json();

                // Extract external booking ID and UID from response
                $externalBookingId = null;
                $externalBookingUid = null;
                if (isset($responseData['data']['id'])) {
                    $externalBookingId = $responseData['data']['id'];
                } elseif (isset($responseData['booking_id'])) {
                    $externalBookingId = $responseData['booking_id'];
                }
                if (isset($responseData['data']['uid'])) {
                    $externalBookingUid = $responseData['data']['uid'];
                }

                return [
                    'success' => true,
                    'external_booking_id' => $externalBookingId,
                    'external_booking_uid' => $externalBookingUid,
                    'message' => 'Booking created successfully in external system',
                    'response_data' => $responseData
                ];
            } else {
                Log::error('External Booking API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'data' => $externalBookingData
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to create booking in external system: ' . $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('External Booking Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'booking_data' => $bookingData
            ]);

            return [
                'success' => false,
                'message' => 'Error creating external booking: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate a customer ID if not exists
     */
    private function generateCustomerId($user)
    {
        // Generate a UUID-like customer ID
        $customerId = '019821eb-' . substr(md5($user->id . $user->email), 0, 4) . '-' .
                     substr(md5($user->name), 0, 4) . '-' .
                     substr(md5(time()), 0, 4) . '-' .
                     substr(md5(uniqid()), 0, 12);

        // Update user with the generated customer ID
        $user->update(['pointsys_customer_id' => $customerId]);

        return $customerId;
    }

    /**
     * Find vehicle by plate number in external API
     */
    private function findVehicleByPlate($plateNumber)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json'
            ])->get('https://rlapp.rentluxuria.com/api/vehicles');

            if ($response->successful()) {
                $vehicles = $response->json();

                foreach ($vehicles as $vehicle) {
                    if (isset($vehicle['plate_number']) && $vehicle['plate_number'] === $plateNumber) {
                        return $vehicle['id'];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error finding vehicle by plate number', [
                'plate_number' => $plateNumber,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Format date for external API
     */
    private function formatDateTime($date)
    {
        // Convert date to ISO 8601 format with time
        $dateTime = \Carbon\Carbon::parse($date);

        // Set pickup time to 10:00 AM and return time to 6:00 PM
        if (strpos($date, 'start_date') !== false || strpos($date, 'pickup') !== false) {
            $dateTime->setTime(10, 0, 0);
        } else {
            $dateTime->setTime(18, 0, 0);
        }

        return $dateTime->toISOString();
    }

    /**
     * Map emirate to pickup location
     */
    private function mapEmirateToLocation($emirate)
    {
        $locationMap = [
            'Dubai' => 'مطار دبي الدولي',
            'Abu Dhabi' => 'مطار أبو ظبي الدولي',
            'Sharjah' => 'مطار الشارقة الدولي',
            'Ajman' => 'مركز عجمان',
            'Umm Al Quwain' => 'مركز أم القيوين',
            'Ras Al Khaimah' => 'مطار رأس الخيمة',
            'Fujairah' => 'مطار الفجيرة'
        ];

        return $locationMap[$emirate] ?? $emirate;
    }

    /**
     * Update local booking with external booking ID
     */
    public function updateLocalBookingWithExternalId($localBookingId, $externalBookingId)
    {
        try {
            $booking = Booking::findOrFail($localBookingId);
            $booking->update([
                'external_reservation_id' => $externalBookingId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating local booking with external ID', [
                'local_booking_id' => $localBookingId,
                'external_booking_id' => $externalBookingId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Update external booking status
     */
    public function updateExternalBookingStatus($externalBookingIdOrUid, $status, bool $isUid = false)
    {
        try {
            $baseUrl = rtrim(config('services.rlapp.base_url', 'https://rlapp.rentluxuria.com'), '/');
            $prefixes = $this->useTest ? ['/api/v1/test', '/api/v1'] : ['/api/v1', '/api/v1/test'];
            $paths = $isUid
                ? [
                    '/reservations/by-uid/' . $externalBookingIdOrUid . '/status',
                ]
                : [
                    '/reservations/' . $externalBookingIdOrUid . '/status',
                    '/custom-reservation/' . $externalBookingIdOrUid . '/status',
                ];
            $endpoints = [];
            foreach ($prefixes as $p) {
                foreach ($paths as $path) {
                    $endpoints[] = $baseUrl . $p . $path;
                }
            }

            // Candidate header strategies (try the exact header first as per API doc)
            $headerStrategies = [
                // Exact header per provided spec
                fn () => [
                    'X-API-KEY' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                // Alternative headers used in other endpoints
                fn () => [
                    'X-RLAPP-KEY' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                fn () => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ];

            // HTTP methods to try
            $methods = ['patch', 'post'];

            foreach ($endpoints as $endpoint) {
                foreach ($headerStrategies as $headersFactory) {
                    $headers = $headersFactory();

                    foreach ($methods as $method) {
                        $response = Http::withHeaders($headers)->$method($endpoint, [
                            'status' => $status
                        ]);

                        Log::info('External Booking Status Update Attempt', [
                            'external_booking_id_or_uid' => $externalBookingIdOrUid,
                            'status' => $status,
                            'update_url' => $endpoint,
                            'method' => strtoupper($method),
                            'headers_used' => array_keys($headers),
                            'response_status' => $response->status(),
                            'response_body' => $response->body()
                        ]);

                        if ($response->successful()) {
                            return [
                                'success' => true,
                                'message' => 'External booking status updated successfully'
                            ];
                        }
                    }
                }
            }

            // If we reach here, all attempts failed
            return [
                'success' => false,
                'message' => 'Failed to update external booking status after multiple attempts'
            ];

        } catch (\Exception $e) {
            Log::error('External Booking Status Update Exception', [
                'external_booking_id_or_uid' => $externalBookingIdOrUid,
                'status' => $status,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error updating external booking status: ' . $e->getMessage()
            ];
        }
    }
}
