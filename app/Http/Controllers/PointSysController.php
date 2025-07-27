<?php

namespace App\Http\Controllers;

use App\Services\PointSysService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PointSysController extends Controller
{
    private PointSysService $pointSysService;

    public function __construct(PointSysService $pointSysService)
    {
        $this->pointSysService = $pointSysService;
    }

        /**
     * Register a new customer
     */
    public function registerCustomer(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $customerData = $request->only(['name', 'email', 'phone']);

        $result = $this->pointSysService->registerCustomer($customerData);

        if ($result === null) {
            return response()->json(['error' => 'Failed to register customer'], 500);
        }

        return response()->json($result, 201);
    }

    /**
     * Get customer balance
     */
    public function getCustomerBalance($customerId): JsonResponse
    {
        $balance = $this->pointSysService->getCustomerBalance($customerId);

        if ($balance === null) {
            return response()->json(['error' => 'Failed to fetch customer balance'], 500);
        }

        return response()->json($balance);
    }

    /**
     * Add points to customer
     */
    public function addPointsToCustomer(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|string',
            'points' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'reference_id' => 'nullable|string|max:255',
        ]);

        $result = $this->pointSysService->addPointsToCustomer(
            $request->customer_id,
            $request->points,
            $request->description ?? '',
            $request->reference_id ?? ''
        );

        if ($result === null) {
            return response()->json(['error' => 'Failed to add points'], 500);
        }

        return response()->json($result);
    }

    /**
     * Get available rewards
     */
    public function getRewards(): JsonResponse
    {
        $rewards = $this->pointSysService->getRewards();

        if ($rewards === null) {
            return response()->json(['error' => 'Failed to fetch rewards'], 500);
        }

        return response()->json($rewards);
    }

    /**
     * Redeem a reward
     */
    public function redeemReward(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|string',
            'reward_id' => 'required|integer|min:1',
        ]);

        $result = $this->pointSysService->redeemReward(
            $request->customer_id,
            $request->reward_id
        );

        if ($result === null) {
            return response()->json(['error' => 'Failed to redeem reward'], 500);
        }

        return response()->json($result);
    }
}
