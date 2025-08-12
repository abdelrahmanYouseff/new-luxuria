<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ExternalCustomerService
{
    private $apiUrl = 'https://rlapp.rentluxuria.com/api/v1/test/customers';
    private $apiKey = '[REDACTED_RLAPP_API_KEY]'; // Use the same API key as other services

    /**
     * Create a customer in the external API
     */
    public function createExternalCustomer($userData)
    {
        try {
            // Prepare the request data for external API
            $externalCustomerData = [
                'first_name' => $this->extractFirstName($userData['name']),
                'last_name' => $this->extractLastName($userData['name']),
                'email' => $userData['email'] ?? null,
                'phone' => $userData['phone'] ?? '0501234567', // Default phone if not provided
                'secondary_identification_type' => 'passport',
                'drivers_license_number' => 'PT1000',
                'drivers_license_expiry' => '2029-10-10T00:00:00.000000Z',
                'passport_number' => 'PT1000',
                'passport_expiry' => '2029-10-17T00:00:00.000000Z',
                'country' => 'United Arab Emirates',
                'nationality' => 'Jordanian',
                'status' => 'active',
                'payment_terms' => 'cash',
                'customer_type' => 'local',
                'vat_registration_country' => 'AE'
            ];

            // Make the API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->apiUrl, $externalCustomerData);

            // Log the request and response for debugging
            Log::info('External Customer API Request', [
                'url' => $this->apiUrl,
                'data' => $externalCustomerData,
                'response_status' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Extract customer ID from response
                $externalCustomerId = null;
                if (isset($responseData['customer']['id'])) {
                    $externalCustomerId = $responseData['customer']['id'];
                } elseif (isset($responseData['data']['id'])) {
                    $externalCustomerId = $responseData['data']['id'];
                } elseif (isset($responseData['customer_id'])) {
                    $externalCustomerId = $responseData['customer_id'];
                }

                return [
                    'success' => true,
                    'external_customer_id' => $externalCustomerId,
                    'message' => 'Customer created successfully in external system',
                    'response_data' => $responseData
                ];
            } else {
                Log::error('External Customer API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'data' => $externalCustomerData
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to create customer in external system: ' . $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('External Customer Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_data' => $userData
            ]);

            return [
                'success' => false,
                'message' => 'Error creating external customer: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Extract first name from full name
     */
    private function extractFirstName($fullName)
    {
        $nameParts = explode(' ', trim($fullName));
        return $nameParts[0] ?? $fullName;
    }

    /**
     * Extract last name from full name
     */
    private function extractLastName($fullName)
    {
        $nameParts = explode(' ', trim($fullName));
        if (count($nameParts) > 1) {
            return implode(' ', array_slice($nameParts, 1));
        }
        // If no last name, use the first name as last name to avoid validation error
        return $nameParts[0] ?? 'User';
    }

    /**
     * Update local user with external customer ID
     */
    public function updateLocalUserWithExternalId($userId, $externalCustomerId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->update([
                'pointsys_customer_id' => $externalCustomerId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating local user with external ID', [
                'user_id' => $userId,
                'external_customer_id' => $externalCustomerId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
