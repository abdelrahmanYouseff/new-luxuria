<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CouponWebsite;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CouponWebsiteController extends Controller
{
    /**
     * عرض صفحة الكوبونات
     */
    public function index()
    {
        $coupons = CouponWebsite::orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('Admin/CouponWebsite/Index', [
            'coupons' => $coupons
        ]);
    }

    /**
     * عرض صفحة إنشاء كوبون جديد
     */
    public function create()
    {
        return Inertia::render('Admin/CouponWebsite/Create');
    }

    /**
     * حفظ كوبون جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupon_website,code|max:50',
            'discount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
            'expire_at' => 'required|date|after:today',
        ]);

        // Validate percentage discount
        if ($request->discount_type === 'percentage' && $request->discount > 100) {
            return back()->withErrors(['discount' => 'Percentage discount cannot exceed 100%']);
        }

        CouponWebsite::create([
            'code' => strtoupper($request->code),
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
            'expire_at' => $request->expire_at,
        ]);

        return redirect()->route('admin.coupon-website.index')
            ->with('success', 'Coupon created successfully');
    }

    /**
     * عرض صفحة تعديل كوبون
     */
    public function edit(CouponWebsite $coupon)
    {
        return Inertia::render('Admin/CouponWebsite/Edit', [
            'coupon' => $coupon
        ]);
    }

    /**
     * تحديث كوبون
     */
    public function update(Request $request, CouponWebsite $coupon)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupon_website,code,' . $coupon->id,
            'discount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
            'expire_at' => 'required|date',
        ]);

        // Validate percentage discount
        if ($request->discount_type === 'percentage' && $request->discount > 100) {
            return back()->withErrors(['discount' => 'Percentage discount cannot exceed 100%']);
        }

        $coupon->update([
            'code' => strtoupper($request->code),
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
            'expire_at' => $request->expire_at,
        ]);

        return redirect()->route('admin.coupon-website.index')
            ->with('success', 'Coupon updated successfully');
    }

    /**
     * حذف كوبون
     */
    public function destroy(CouponWebsite $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupon-website.index')
            ->with('success', 'Coupon deleted successfully');
    }
}
