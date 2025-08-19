<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CouponWebsite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    /**
     * التحقق من كود الخصم
     */
    public function validateCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $code = strtoupper($request->code);

        // البحث عن الكوبون
        $coupon = CouponWebsite::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon code not found',
                'data' => null
            ], 404);
        }

        // التحقق من انتهاء الصلاحية
        if (now()->isAfter($coupon->expire_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon has expired',
                'data' => null
            ], 400);
        }

        // إرجاع بيانات الكوبون
        return response()->json([
            'success' => true,
            'message' => 'Coupon is valid',
            'data' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount,
                'expire_at' => $coupon->expire_at->format('Y-m-d H:i:s'),
                'is_active' => true
            ]
        ], 200);
    }

    /**
     * الحصول على جميع الكوبونات النشطة
     */
    public function getActiveCoupons(): JsonResponse
    {
        $coupons = CouponWebsite::where('expire_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($coupon) {
                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => $coupon->discount,
                    'expire_at' => $coupon->expire_at->format('Y-m-d H:i:s'),
                    'is_active' => true
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Active coupons retrieved successfully',
            'data' => $coupons
        ], 200);
    }
}
