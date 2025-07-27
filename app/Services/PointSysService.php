<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PointSysService
{
    private string $apiKey;
    private string $baseUrl;
    private bool $useMock;

    public function __construct()
    {
        $this->apiKey = config('services.pointsys.api_key');
        $this->baseUrl = config('services.pointsys.base_url');

        // Use mock if Stripe keys are not configured (dev/testing mode)
        $stripeKey = config('services.stripe.secret_key');
        $this->useMock = !$stripeKey || str_contains($stripeKey, 'your_secret_key_here');
    }

    /**
     * Make a request to PointSys API
     */
    private function makeRequest(string $endpoint, array $data = [], string $method = 'GET')
    {
        // Use mock responses for testing
        if ($this->useMock) {
            return $this->getMockResponse($endpoint, $data, $method);
        }

        try {
            $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            // Log the request details for debugging
            Log::info('PointSys API Request', [
                'method' => $method,
                'url' => $url,
                'headers' => $headers,
                'data' => $data,
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->$method($url, $data);

            // Log the response details for debugging
            Log::info('PointSys API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            // Handle specific error cases
            if ($response->status() === 401) {
                Log::error('PointSys API Authentication Error', [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'message' => 'API Key is invalid or expired. Please check your API key configuration.'
                ]);
            } else {
                Log::error('PointSys API Error', [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('PointSys API Exception', [
                'endpoint' => $endpoint,
                'method' => $method,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get mock responses for testing
     */
    private function getMockResponse(string $endpoint, array $data = [], string $method = 'GET')
    {
        Log::info('PointSys Mock API Request', [
            'method' => $method,
            'endpoint' => $endpoint,
            'data' => $data,
        ]);

        switch ($endpoint) {
            case 'customers/register':
                if ($method === 'POST') {
                    $customerId = rand(1000, 9999);
                    $response = [
                        'status' => 'success',
                        'message' => 'تم تسجيل العميل بنجاح',
                        'data' => [
                            'customer_id' => $customerId,
                            'name' => $data['name'] ?? 'Test User',
                            'email' => $data['email'] ?? 'test@example.com',
                            'phone' => $data['phone'] ?? '0501234567',
                            'points_balance' => 0
                        ]
                    ];
                    Log::info('PointSys Mock API Response', $response);
                    return $response;
                }
                break;

            case (preg_match('/^customers\/\d+\/balance$/', $endpoint) ? true : false):
                if ($method === 'GET') {
                    $customerId = (int) explode('/', $endpoint)[1];
                    $response = [
                        'status' => 'success',
                        'data' => [
                            'customer_id' => $customerId,
                            'name' => 'Test User',
                            'points_balance' => rand(0, 1000),
                            'total_earned' => rand(1000, 5000),
                            'total_redeemed' => rand(0, 2000)
                        ]
                    ];
                    Log::info('PointSys Mock API Response', $response);
                    return $response;
                }
                break;

            case 'customers/points/add':
                if ($method === 'POST') {
                    $response = [
                        'status' => 'success',
                        'message' => 'تم إضافة النقاط بنجاح',
                        'data' => [
                            'transaction_id' => rand(10000, 99999),
                            'customer_id' => $data['customer_id'] ?? 1,
                            'points_added' => $data['points'] ?? 100,
                            'new_balance' => rand(100, 2000),
                            'description' => $data['description'] ?? 'Test points',
                            'reference_id' => $data['reference_id'] ?? 'TEST_REF'
                        ]
                    ];
                    Log::info('PointSys Mock API Response', $response);
                    return $response;
                }
                break;

            case 'rewards':
                if ($method === 'GET') {
                    $response = [
                        'status' => 'success',
                        'data' => [
                            [
                                'id' => 1,
                                'title' => 'خصم 10%',
                                'description' => 'خصم 10% على المشتريات',
                                'points_required' => 100,
                                'type' => 'discount',
                                'value' => 10,
                                'status' => 'active'
                            ],
                            [
                                'id' => 2,
                                'title' => 'هدية مجانية',
                                'description' => 'هدية مجانية مع الطلب',
                                'points_required' => 200,
                                'type' => 'gift',
                                'value' => null,
                                'status' => 'active'
                            ],
                            [
                                'id' => 3,
                                'title' => 'خصم 20%',
                                'description' => 'خصم 20% على الخدمات المميزة',
                                'points_required' => 500,
                                'type' => 'discount',
                                'value' => 20,
                                'status' => 'active'
                            ]
                        ]
                    ];
                    Log::info('PointSys Mock API Response', $response);
                    return $response;
                }
                break;

            case 'rewards/redeem':
                if ($method === 'POST') {
                    $response = [
                        'status' => 'success',
                        'message' => 'تم استبدال المكافأة بنجاح',
                        'data' => [
                            'redemption_id' => rand(10000, 99999),
                            'customer_id' => $data['customer_id'] ?? 1,
                            'reward_id' => $data['reward_id'] ?? 1,
                            'points_used' => rand(100, 500),
                            'remaining_balance' => rand(0, 1000)
                        ]
                    ];
                    Log::info('PointSys Mock API Response', $response);
                    return $response;
                }
                break;
        }

        return null;
    }

    /**
     * Register a new customer
     */
    public function registerCustomer(array $customerData)
    {
        return $this->makeRequest('customers/register', $customerData, 'POST');
    }

    /**
     * Get customer balance
     */
    public function getCustomerBalance(int $customerId)
    {
        return $this->makeRequest("customers/{$customerId}/balance");
    }

    /**
     * Add points to customer
     */
    public function addPointsToCustomer(int $customerId, int $points, string $description = '', string $referenceId = '')
    {
        return $this->makeRequest('customers/points/add', [
            'customer_id' => $customerId,
            'points' => $points,
            'description' => $description,
            'reference_id' => $referenceId,
        ], 'POST');
    }

    /**
     * Get available rewards
     */
    public function getRewards()
    {
        return $this->makeRequest('rewards');
    }

    /**
     * Redeem a reward
     */
    public function redeemReward(int $customerId, int $rewardId)
    {
        return $this->makeRequest('rewards/redeem', [
            'customer_id' => $customerId,
            'reward_id' => $rewardId,
        ], 'POST');
    }

    /**
     * Get customer points balance by user
     */
    public function getCustomerPointsByUser(User $user)
    {
        if (!$user->pointsys_customer_id) {
            return null;
        }

        return $this->getCustomerBalance($user->pointsys_customer_id);
    }

    /**
     * Add points to customer by user
     */
    public function addPointsToCustomerByUser(User $user, int $points, string $description = '', string $referenceId = '')
    {
        if (!$user->pointsys_customer_id) {
            return null;
        }

        return $this->addPointsToCustomer($user->pointsys_customer_id, $points, $description, $referenceId);
    }
}
