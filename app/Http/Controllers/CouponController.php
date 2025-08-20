<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\PointSysService;

class CouponController extends Controller
{
    public function index()
    {
        try {
            // استخدام PointSysService لجلب الكوبونات
            $pointSysService = new PointSysService();
            $response = $pointSysService->getCoupons();

            if ($response && isset($response['data'])) {
                $coupons = $response['data'];

                // تحويل البيانات إلى الشكل المطلوب للصفحة
                $formattedCoupons = collect($coupons)->map(function ($coupon) {
                    $formattedCoupon = [
                        'id' => $coupon['id'] ?? uniqid(),
                        'code' => $coupon['code'] ?? '',
                        'name' => $coupon['title'] ?? $coupon['name'] ?? 'كوبون خصم',
                        'description' => $coupon['description'] ?? '',
                        'discount_value' => $coupon['discount_value'] ?? $coupon['amount'] ?? 0,
                        'discount_type' => $coupon['discount_type'] ?? 'fixed',
                        'minimum_amount' => $coupon['min_order_value'] ?? $coupon['minimum_amount'] ?? 0,
                        'points_required' => $coupon['points_required'] ?? 0,
                        'usage_limit' => $coupon['usage_limit'] ?? null,
                        'used_count' => $coupon['used_count'] ?? 0,
                        'status' => $coupon['is_active'] ? 'active' : 'inactive',
                        'is_valid' => $coupon['is_active'] ?? true,
                        'starts_at' => $coupon['start_date'] ?? $coupon['created_at'] ?? now(),
                        'expires_at' => $coupon['end_date'] ?? $coupon['expires_at'] ?? '2025-12-31',
                        'formatted_price' => $this->formatCouponPrice(
                            $coupon['discount_value'] ?? $coupon['amount'] ?? 0,
                            $coupon['discount_type'] ?? 'fixed'
                        ),
                        'price' => $coupon['price'] ?? $coupon['points_required'] ?? 0
                    ];

                    Log::info('Formatted coupon from PointSys: ' . json_encode($formattedCoupon));
                    return $formattedCoupon;
                })->filter(function ($coupon) {
                    // عرض الكوبونات النشطة والصالحة فقط
                    return $coupon['status'] === 'active' && $coupon['is_valid'] === true;
                })->values();

                Log::info('Final formatted coupons count from PointSys: ' . $formattedCoupons->count());
                return view('coupons', compact('formattedCoupons'));
            } else {
                Log::warning('No valid coupon data from PointSys API');
                return view('coupons', ['formattedCoupons' => collect([])]);
            }

        } catch (\Exception $e) {
            Log::error('Exception occurred while fetching coupons: ' . $e->getMessage());
            return view('coupons', ['formattedCoupons' => collect([])]);
        }
    }



    /**
     * Format coupon price for display
     */
    private function formatCouponPrice($value, $type)
    {
        if ($type === 'percentage' || $type === 'percent') {
            return $value . '%';
        } else {
            return number_format($value) . ' AED';
        }
    }

    /**
     * Get coupons as JSON for frontend API
     */
    public function getCouponsApi()
    {
        try {
            // استخدام PointSysService لجلب الكوبونات
            $pointSysService = new PointSysService();
            $response = $pointSysService->getCoupons();

            if ($response && isset($response['data'])) {
                $coupons = $response['data'];

                // تحويل البيانات إلى الشكل المطلوب للـ API
                $formattedCoupons = collect($coupons)->map(function ($coupon) {
                    return [
                        'id' => $coupon['id'] ?? uniqid(),
                        'code' => $coupon['code'] ?? '',
                        'title' => $coupon['title'] ?? $coupon['name'] ?? 'كوبون خصم',
                        'description' => $coupon['description'] ?? '',
                        'discount_type' => $coupon['discount_type'] ?? 'fixed',
                        'discount_value' => $coupon['discount_value'] ?? $coupon['amount'] ?? 0,
                        'min_order_value' => $coupon['min_order_value'] ?? $coupon['minimum_amount'] ?? 0,
                        'max_discount' => $coupon['max_discount'] ?? null,
                        'usage_limit' => $coupon['usage_limit'] ?? null,
                        'used_count' => $coupon['used_count'] ?? 0,
                        'is_active' => $coupon['is_active'] ?? true,
                        'start_date' => $coupon['start_date'] ?? null,
                        'end_date' => $coupon['end_date'] ?? null,
                        'applicable_categories' => $coupon['applicable_categories'] ?? [],
                        'applicable_products' => $coupon['applicable_products'] ?? [],
                        'formatted_discount' => $this->formatCouponPrice(
                            $coupon['discount_value'] ?? $coupon['amount'] ?? 0,
                            $coupon['discount_type'] ?? 'fixed'
                        ),
                        'price' => $coupon['price'] ?? $coupon['points_required'] ?? 0,
                        'is_expired' => $this->isCouponExpired($coupon),
                        'days_remaining' => $this->getCouponDaysRemaining($coupon)
                    ];
                })->filter(function ($coupon) {
                    // عرض الكوبونات النشطة والصالحة فقط
                    return $coupon['is_active'] && !$coupon['is_expired'];
                })->values();

                Log::info('API coupons retrieved from PointSys: ' . $formattedCoupons->count());

                return response()->json([
                    'success' => true,
                    'message' => 'تم جلب الكوبونات بنجاح',
                    'data' => [
                        'coupons' => $formattedCoupons,
                        'total_count' => $formattedCoupons->count(),
                        'active_count' => $formattedCoupons->where('is_active', true)->where('is_expired', false)->count()
                    ]
                ]);
            } else {
                Log::warning('No valid coupon data from PointSys API for frontend');
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد كوبونات متاحة حالياً',
                    'data' => [
                        'coupons' => [],
                        'total_count' => 0,
                        'active_count' => 0
                    ]
                ], 200);
            }

        } catch (\Exception $e) {
            Log::error('Exception occurred while fetching coupons for API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الكوبونات',
                'error' => $e->getMessage(),
                'data' => [
                    'coupons' => [],
                    'total_count' => 0,
                    'active_count' => 0
                ]
            ], 500);
        }
    }



    /**
     * Helper method to check if coupon is expired
     */
    private function isCouponExpired($coupon)
    {
        if (!isset($coupon['end_date']) || empty($coupon['end_date'])) {
            return false;
        }

        $endDate = strtotime($coupon['end_date']);
        return $endDate < time();
    }

    /**
     * Helper method to get remaining days for coupon
     */
    private function getCouponDaysRemaining($coupon)
    {
        if (!isset($coupon['end_date']) || empty($coupon['end_date'])) {
            return null;
        }

        $endDate = strtotime($coupon['end_date']);
        $currentTime = time();
        $diff = $endDate - $currentTime;

        if ($diff <= 0) {
            return 0;
        }

        return ceil($diff / (60 * 60 * 24));
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
