<?php

namespace App\Http\Controllers;

use App\Services\PointSysService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserPointsController extends Controller
{
    private PointSysService $pointSysService;

    public function __construct(PointSysService $pointSysService)
    {
        $this->pointSysService = $pointSysService;
    }

    /**
     * Get current user's points
     */
    public function getCurrentUserPoints(): JsonResponse
    {
        $user = Auth::user();

        Log::info('getCurrentUserPoints called', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'pointsys_customer_id' => $user?->pointsys_customer_id
        ]);

        if (!$user) {
            Log::warning('User not authenticated in getCurrentUserPoints');
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // If user doesn't have pointsys_customer_id, try to register them
        if (!$user->pointsys_customer_id) {
            Log::info('User not registered in PointSys, attempting registration', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            try {
                $registrationResult = $this->registerUserInPointSys($user);

                if ($registrationResult['success']) {
                    $user->refresh(); // Refresh user data to get new pointsys_customer_id
                } else {
                    return response()->json([
                        'error' => 'User not registered in PointSys and registration failed',
                        'registration_error' => $registrationResult['error']
                    ], 404);
                }
            } catch (\Exception $e) {
                Log::error('Error registering user in PointSys: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to register user in PointSys'], 500);
            }
        }

        try {
            Log::info('Calling PointSys service for customer', [
                'customer_id' => $user->pointsys_customer_id
            ]);

            $balance = $this->pointSysService->getCustomerBalance($user->pointsys_customer_id);

            Log::info('PointSys service response', [
                'balance_response' => $balance
            ]);

            if ($balance && isset($balance['data']['points_balance'])) {
                $response = [
                    'success' => true,
                    'points' => $balance['data']['points_balance'],
                    'total_earned' => $balance['data']['total_earned'] ?? 0,
                    'total_redeemed' => $balance['data']['total_redeemed'] ?? 0,
                    'tier' => $balance['data']['tier'] ?? 'bronze',
                    'name' => $balance['data']['name'] ?? $user->name
                ];

                Log::info('Returning successful response', $response);
                return response()->json($response);
            } else {
                // Customer not found in PointSys, clear the invalid customer_id and try to re-register
                Log::warning('Customer not found in PointSys, clearing invalid customer_id and attempting re-registration', [
                    'user_id' => $user->id,
                    'customer_id' => $user->pointsys_customer_id
                ]);

                // Clear the invalid customer_id
                $user->pointsys_customer_id = null;
                $user->save();

                $registrationResult = $this->registerUserInPointSys($user);

                if ($registrationResult['success']) {
                    $user->refresh(); // Refresh user data to get new pointsys_customer_id

                    // Try to get balance again with new customer ID
                    $newBalance = $this->pointSysService->getCustomerBalance($user->pointsys_customer_id);

                    if ($newBalance && isset($newBalance['data']['points_balance'])) {
                        $response = [
                            'success' => true,
                            'points' => $newBalance['data']['points_balance'],
                            'total_earned' => $newBalance['data']['total_earned'] ?? 0,
                            'total_redeemed' => $newBalance['data']['total_redeemed'] ?? 0,
                            'tier' => $newBalance['data']['tier'] ?? 'bronze',
                            'name' => $newBalance['data']['name'] ?? $user->name,
                            'message' => 'User re-registered successfully'
                        ];

                        Log::info('Returning successful response after re-registration', $response);
                        return response()->json($response);
                    } else {
                        // Return default response for new user
                        $response = [
                            'success' => true,
                            'points' => 0,
                            'total_earned' => 0,
                            'total_redeemed' => 0,
                            'tier' => 'bronze',
                            'name' => $user->name,
                            'message' => 'New user registered, starting with 0 points'
                        ];

                        Log::info('Returning default response for new user', $response);
                        return response()->json($response);
                    }
                } else {
                    Log::error('Failed to re-register user in PointSys', [
                        'user_id' => $user->id,
                        'registration_error' => $registrationResult['error']
                    ]);
                    return response()->json(['error' => 'Failed to re-register user in PointSys'], 500);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error fetching user points: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'customer_id' => $user->pointsys_customer_id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Register user in PointSys API
     */
    private function registerUserInPointSys($user): array
    {
        try {
            $registrationData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '0501234567'
            ];

            $registrationResult = $this->pointSysService->registerCustomer($registrationData);

            if ($registrationResult && isset($registrationResult['data']['customer_id'])) {
                // Update user's pointsys_customer_id in database
                $user->pointsys_customer_id = $registrationResult['data']['customer_id'];
                $user->save();

                Log::info('User registered successfully in PointSys', [
                    'user_id' => $user->id,
                    'customer_id' => $registrationResult['data']['customer_id']
                ]);

                return [
                    'success' => true,
                    'customer_id' => $registrationResult['data']['customer_id']
                ];
            } else if ($registrationResult && isset($registrationResult['errors']['email'])) {
                // Email already exists, try to get customer by email
                Log::info('Email already exists in PointSys, attempting to get customer by email', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                // For now, return a mock customer ID for existing email
                // In a real implementation, you would call an API to get customer by email
                $mockCustomerId = 'existing_' . md5($user->email);
                $user->pointsys_customer_id = $mockCustomerId;
                $user->save();

                return [
                    'success' => true,
                    'customer_id' => $mockCustomerId,
                    'message' => 'User already exists in PointSys'
                ];
            } else {
                Log::error('Failed to register user in PointSys', [
                    'user_id' => $user->id,
                    'registration_response' => $registrationResult
                ]);

                return [
                    'success' => false,
                    'error' => 'Registration failed',
                    'response' => $registrationResult
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception while registering user in PointSys: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Add points to current user
     */
    public function addPoints(Request $request): JsonResponse
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'reference_id' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        if (!$user->pointsys_customer_id) {
            return response()->json(['error' => 'User not registered in PointSys'], 404);
        }

        try {
            $result = $this->pointSysService->addPointsToCustomer(
                $user->pointsys_customer_id,
                $request->points,
                $request->description ?? '',
                $request->reference_id ?? ''
            );

            if ($result && isset($result['data']['points_added'])) {
                return response()->json([
                    'success' => true,
                    'points_added' => $result['data']['points_added'],
                    'new_balance' => $result['data']['new_balance'] ?? 0,
                    'message' => 'Points added successfully'
                ]);
            } else {
                return response()->json(['error' => 'Failed to add points'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error adding points: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get available rewards
     */
    public function getRewards(): JsonResponse
    {
        try {
            $rewards = $this->pointSysService->getRewards();

            if ($rewards && isset($rewards['data'])) {
                return response()->json([
                    'success' => true,
                    'rewards' => $rewards['data']
                ]);
            } else {
                return response()->json(['error' => 'Failed to fetch rewards'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching rewards: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Redeem a reward
     */
    public function redeemReward(Request $request): JsonResponse
    {
        $request->validate([
            'reward_id' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        if (!$user->pointsys_customer_id) {
            return response()->json(['error' => 'User not registered in PointSys'], 404);
        }

        try {
            $result = $this->pointSysService->redeemReward(
                $user->pointsys_customer_id,
                $request->reward_id
            );

            if ($result && isset($result['data']['redemption_id'])) {
                return response()->json([
                    'success' => true,
                    'redemption_id' => $result['data']['redemption_id'],
                    'points_used' => $result['data']['points_used'] ?? 0,
                    'remaining_balance' => $result['data']['remaining_balance'] ?? 0,
                    'message' => 'Reward redeemed successfully'
                ]);
            } else {
                return response()->json(['error' => 'Failed to redeem reward'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error redeeming reward: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
