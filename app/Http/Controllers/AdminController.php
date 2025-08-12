<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\Coupon;
use App\Models\CouponInvoice;

class AdminController extends Controller
{
    public function dashboard()
    {
        // إحصائيات خاصة بالمدير
        $stats = [
            'total_users' => User::count(),
            'total_vehicles' => Vehicle::count(),
            'total_reservations' => Reservation::count(),
            'total_transactions' => Transaction::count(),
            'total_coupons' => Coupon::count(),
            'total_coupon_invoices' => CouponInvoice::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'available_vehicles' => Vehicle::where('status', 'available')->count(),
            'rented_vehicles' => Vehicle::where('status', 'rented')->count(),
        ];

        // آخر المستخدمين المسجلين
        $recentUsers = User::latest()->take(5)->get(['name', 'email', 'created_at', 'role']);

        // آخر السيارات المضافة
        $recentVehicles = Vehicle::latest()->take(5)->get(['make', 'model', 'year', 'status', 'created_at']);

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentVehicles'));
    }

    public function users()
    {
        $users = User::paginate(20);
        return view('admin.users', compact('users'));
    }

    public function vehicles()
    {
        $vehicles = Vehicle::orderBy('daily_rate', 'desc')->paginate(20);
        return view('admin.vehicles', compact('vehicles'));
    }

    public function analytics()
    {
        // بيانات تحليلية للمدير
        $monthlyRegistrations = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();

        $vehiclesByCategory = Vehicle::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        return view('admin.analytics', compact('monthlyRegistrations', 'vehiclesByCategory'));
    }
}
