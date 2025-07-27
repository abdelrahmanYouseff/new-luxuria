<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\CouponInvoice;
use Inertia\Inertia;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // إحصائيات خاصة بالمستخدم
        $stats = [
            'my_reservations' => Reservation::where('user_id', $user->id)->count(),
            'my_transactions' => Transaction::where('user_id', $user->id)->count(),
            'my_coupon_invoices' => CouponInvoice::where('user_id', $user->id)->count(),
            'pending_reservations' => Reservation::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed_reservations' => Reservation::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        // آخر الحجوزات
        $recentReservations = Reservation::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // آخر الفواتير
        $recentInvoices = CouponInvoice::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('stats', 'recentReservations', 'recentInvoices'));
    }

    public function profile()
    {
        return view('user.profile');
    }

    public function reservations()
    {
        $user = Auth::user();
        $reservations = Reservation::where('user_id', $user->id)->paginate(10);
        return view('user.reservations', compact('reservations'));
    }

    public function invoices()
    {
        $user = Auth::user();
        $invoices = CouponInvoice::where('user_id', $user->id)->paginate(10);
        return view('user.invoices', compact('invoices'));
    }

    /**
     * View user invoices with sidebar and header (Inertia.js page)
     */
    public function viewInvoices()
    {
        $user = Auth::user();

        // Get user's invoices with pagination
        $invoices = CouponInvoice::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate user's invoice statistics
        $stats = [
            'total_invoices' => CouponInvoice::where('user_id', $user->id)->count(),
            'total_amount' => CouponInvoice::where('user_id', $user->id)->sum('amount'),
            'completed_invoices' => CouponInvoice::where('user_id', $user->id)->where('payment_status', 'completed')->count(),
            'pending_invoices' => CouponInvoice::where('user_id', $user->id)->where('payment_status', 'pending')->count(),
            'failed_invoices' => CouponInvoice::where('user_id', $user->id)->where('payment_status', 'failed')->count(),
        ];

        return Inertia::render('user/ViewInvoices', [
            'invoices' => $invoices,
            'stats' => $stats,
            'user' => $user
        ]);
    }
}
