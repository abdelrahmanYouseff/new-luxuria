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

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        if (!$user->pointsys_customer_id) {
            return response()->json(['error' => 'User not registered in PointSys'], 404);
        }

        try {
            $balance = $this->pointSysService->getCustomerBalance($user->pointsys_customer_id);

            if ($balance && isset($balance['data']['points_balance'])) {
                return response()->json([
                    'success' => true,
                    'points' => $balance['data']['points_balance'],
                    'total_earned' => $balance['data']['total_earned'] ?? 0,
                    'total_redeemed' => $balance['data']['total_redeemed'] ?? 0
                ]);
            } else {
                return response()->json(['error' => 'Failed to fetch points balance'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching user points: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
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
