<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index()
    {
        try {
            // استدعاء API للحصول على الكوبونات
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer [REDACTED_STRIPE_LIVE]',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->get('https://www.pointsys.clarastars.com/api/v1/coupons');

            // إضافة تفاصيل التصحيح
            Log::info('API Response Status: ' . $response->status());
            Log::info('API Response Body: ' . $response->body());



            if ($response->successful()) {
                $responseData = $response->json();

                // التحقق من نجاح الاستجابة ووجود البيانات
                if (isset($responseData['status']) && $responseData['status'] === 'success' && isset($responseData['data'])) {
                    $coupons = $responseData['data'];

                    // تحويل البيانات إلى الشكل المطلوب للصفحة
                    $formattedCoupons = collect($coupons)->map(function ($coupon) {
                        $formattedCoupon = [
                            'id' => $coupon['id'] ?? uniqid(),
                            'code' => $coupon['code'] ?? '',
                            'name' => $coupon['name'] ?? '',
                            'description' => $coupon['description'] ?? '',
                            'discount_value' => $coupon['value'] ?? 0,
                            'discount_type' => $coupon['type'] ?? 'fixed',
                            'minimum_amount' => $coupon['minimum_purchase'] ?? 0,
                            'points_required' => $coupon['points_required'] ?? 0,
                            'usage_limit' => $coupon['usage_limit'] ?? null,
                            'used_count' => $coupon['used_count'] ?? 0,
                            'status' => $coupon['status'] ?? 'active',
                            'is_valid' => $coupon['is_valid'] ?? true,
                            'starts_at' => $coupon['starts_at'] ?? $coupon['created_at'] ?? now(),
                            'expires_at' => $coupon['expires_at'] ?? '2025-12-31',
                            'formatted_price' => $coupon['formatted_price'] ?? '',
                            'price' => $coupon['price'] ?? $coupon['points_required'] ?? 0
                        ];

                        Log::info('Formatted coupon: ' . json_encode($formattedCoupon));
                        return $formattedCoupon;
                    })->filter(function ($coupon) {
                        // عرض الكوبونات النشطة والصالحة فقط
                        return $coupon['status'] === 'active' && $coupon['is_valid'] === true;
                    })->values();

                    Log::info('Final formatted coupons count: ' . $formattedCoupons->count());
                    return view('coupons', compact('formattedCoupons'));
                } else {
                    // في حالة عدم وجود بيانات صحيحة، إضافة كوبونات تجريبية
                    Log::info('No valid API data, using sample coupons');
                    $formattedCoupons = collect([
                        [
                            'id' => 1,
                            'code' => 'WELCOME20',
                            'name' => 'Welcome Coupon',
                            'description' => '20% discount on all services',
                            'discount_value' => 20,
                            'discount_type' => 'percentage',
                            'minimum_amount' => 100,
                            'points_required' => 0,
                            'usage_limit' => 50,
                            'used_count' => 15,
                            'status' => 'active',
                            'is_valid' => true,
                            'starts_at' => now(),
                            'expires_at' => '2025-12-31',
                            'formatted_price' => '20%',
                            'price' => 50
                        ],
                        [
                            'id' => 2,
                            'code' => 'LUXURY50',
                            'name' => 'Luxury Coupon',
                            'description' => '50 AED discount on luxury cars',
                            'discount_value' => 50,
                            'discount_type' => 'fixed',
                            'minimum_amount' => 200,
                            'points_required' => 100,
                            'usage_limit' => 25,
                            'used_count' => 8,
                            'status' => 'active',
                            'is_valid' => true,
                            'starts_at' => now(),
                            'expires_at' => '2025-12-31',
                            'formatted_price' => '50 AED',
                            'price' => 100
                        ],
                        [
                            'id' => 3,
                            'code' => 'PREMIUM100',
                            'name' => 'Premium Coupon',
                            'description' => '100 AED discount on premium vehicles',
                            'discount_value' => 100,
                            'discount_type' => 'fixed',
                            'minimum_amount' => 500,
                            'points_required' => 200,
                            'usage_limit' => 10,
                            'used_count' => 3,
                            'status' => 'active',
                            'is_valid' => true,
                            'starts_at' => now(),
                            'expires_at' => '2025-12-31',
                            'formatted_price' => '100 AED',
                            'price' => 200
                        ]
                    ]);
                    return view('coupons', compact('formattedCoupons'));
                }
            } else {
                // في حالة فشل API، إضافة كوبونات تجريبية
                Log::info('API failed, using sample coupons');
                $formattedCoupons = collect([
                                            [
                            'id' => 1,
                            'code' => 'WELCOME20',
                            'name' => 'Welcome Coupon',
                            'description' => '20% discount on all services',
                            'discount_value' => 20,
                            'discount_type' => 'percentage',
                            'minimum_amount' => 100,
                            'points_required' => 0,
                            'usage_limit' => 50,
                            'used_count' => 15,
                            'status' => 'active',
                            'is_valid' => true,
                            'starts_at' => now(),
                            'expires_at' => '2025-12-31',
                            'formatted_price' => '20%',
                            'price' => 50
                        ],
                        [
                            'id' => 2,
                            'code' => 'LUXURY50',
                            'name' => 'Luxury Coupon',
                            'description' => '50 AED discount on luxury cars',
                            'discount_value' => 50,
                            'discount_type' => 'fixed',
                            'minimum_amount' => 200,
                            'points_required' => 100,
                            'usage_limit' => 25,
                            'used_count' => 8,
                            'status' => 'active',
                            'is_valid' => true,
                            'starts_at' => now(),
                            'expires_at' => '2025-12-31',
                            'formatted_price' => '50 AED',
                            'price' => 100
                        ],
                        [
                            'id' => 3,
                            'code' => 'PREMIUM100',
                            'name' => 'Premium Coupon',
                            'description' => '100 AED discount on premium vehicles',
                            'discount_value' => 100,
                            'discount_type' => 'fixed',
                            'minimum_amount' => 500,
                            'points_required' => 200,
                            'usage_limit' => 10,
                            'used_count' => 3,
                            'status' => 'active',
                            'is_valid' => true,
                            'starts_at' => now(),
                            'expires_at' => '2025-12-31',
                            'formatted_price' => '100 AED',
                            'price' => 200
                        ]
                ]);
                return view('coupons', compact('formattedCoupons'));
            }
        } catch (\Exception $e) {
            // في حالة حدوث خطأ، إضافة كوبونات تجريبية
            Log::error('Exception occurred: ' . $e->getMessage());
            $formattedCoupons = collect([
                                        [
                            'id' => 1,
                            'code' => 'WELCOME20',
                            'name' => 'Welcome Coupon',
                            'description' => '20% discount on all services',
                            'discount_value' => 20,
                            'discount_type' => 'percentage',
                            'minimum_amount' => 100,
                            'points_required' => 0,
                            'usage_limit' => 50,
                            'used_count' => 15,
                            'status' => 'active',
                            'is_valid' => true,
                            'starts_at' => now(),
                            'expires_at' => '2025-12-31',
                            'formatted_price' => '20%',
                            'price' => 50
                        ],
                        [
                            'id' => 2,
                            'code' => 'LUXURY50',
                            'name' => 'Luxury Coupon',
                            'description' => '50 AED discount on luxury cars',
                            'discount_value' => 50,
                            'discount_type' => 'fixed',
                            'minimum_amount' => 200,
                            'points_required' => 100,
                            'usage_limit' => 25,
                            'used_count' => 8,
                            'status' => 'active',
                            'is_valid' => true,
                            'starts_at' => now(),
                            'expires_at' => '2025-12-31',
                            'formatted_price' => '50 AED',
                            'price' => 100
                        ],
                        [
                            'id' => 3,
                            'code' => 'PREMIUM100',
                            'name' => 'Premium Coupon',
                            'description' => '100 AED discount on premium vehicles',
                            'discount_value' => 100,
                            'discount_type' => 'fixed',
                            'minimum_amount' => 500,
                            'points_required' => 200,
                            'usage_limit' => 10,
                            'used_count' => 3,
                            'status' => 'active',
                            'is_valid' => true,
                            'starts_at' => now(),
                            'expires_at' => '2025-12-31',
                            'formatted_price' => '100 AED',
                            'price' => 200
                        ]
            ]);
            return view('coupons', compact('formattedCoupons'));
        }
    }

    public function store(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer [REDACTED_STRIPE_LIVE]',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://www.pointsys.clarastars.com/api/v1/coupons', [
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'value' => $request->value,
                'points_required' => $request->points_required,
                'usage_limit' => $request->usage_limit,
                'minimum_purchase' => $request->minimum_purchase,
                'status' => $request->status ?? 'active'
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Coupon created successfully',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create coupon',
                    'error' => $response->json()
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating coupon',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
