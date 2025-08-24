<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CouponInvoiceService;
use App\Services\BookingInvoiceService;
use Inertia\Inertia;
use App\Models\CouponInvoice;
use App\Models\BookingInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    private CouponInvoiceService $couponInvoiceService;
    private BookingInvoiceService $bookingInvoiceService;

    public function __construct(
        CouponInvoiceService $couponInvoiceService,
        BookingInvoiceService $bookingInvoiceService
    ) {
        $this->couponInvoiceService = $couponInvoiceService;
        $this->bookingInvoiceService = $bookingInvoiceService;
    }

    /**
     * Display the invoices index page
     */
    public function index()
    {
        return view('invoices.index');
    }

    /**
     * Get user's invoices via API (combined coupon and booking invoices)
     */
    public function getUserInvoices(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            // Return empty data for non-authenticated users
            return response()->json([
                'success' => true,
                'invoices' => [],
                'stats' => [
                    'total_invoices' => 0,
                    'total_amount' => 0,
                    'completed_invoices' => 0,
                    'failed_invoices' => 0,
                    'booking_invoices' => 0,
                    'coupon_invoices' => 0,
                ],
            ]);
        }

        $invoices = $this->bookingInvoiceService->getCombinedUserInvoices($user, 50);
        $stats = $this->bookingInvoiceService->getCombinedInvoiceStats($user);

        return response()->json([
            'success' => true,
            'invoices' => $invoices,
            'stats' => $stats,
        ]);
    }

    /**
     * Show a specific invoice
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $invoice = $user->couponInvoices()->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Download invoice as PDF
     */
    public function download($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $invoice = $user->couponInvoices()->with('user')->findOrFail($id);

        // Generate PDF from the invoice template
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isPhpEnabled' => true,
                      'isRemoteEnabled' => true,
                      'defaultFont' => 'Arial'
                  ]);

        // Return PDF as download
        return $pdf->download('invoice_' . $invoice->invoice_number . '.pdf');
    }

    /**
     * View invoice as PDF in browser
     */
    public function viewPdf($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $invoice = $user->couponInvoices()->with('user')->findOrFail($id);

        // Generate PDF from the invoice template
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isPhpEnabled' => true,
                      'isRemoteEnabled' => true,
                      'defaultFont' => 'Arial'
                  ]);

        // Return PDF for viewing in browser
        return $pdf->stream('invoice_' . $invoice->invoice_number . '.pdf');
    }

    /**
     * View all invoices (Admin only)
     */
    public function viewAllInvoices()
    {
        // Get all invoices with user relationship
        $invoices = CouponInvoice::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $stats = [
            'total_invoices' => CouponInvoice::count(),
            'total_amount' => CouponInvoice::sum('amount'),
            'completed_invoices' => CouponInvoice::where('payment_status', 'completed')->count(),
            'pending_invoices' => CouponInvoice::where('payment_status', 'pending')->count(),
            'failed_invoices' => CouponInvoice::where('payment_status', 'failed')->count(),
        ];

        return Inertia::render('admin/ViewInvoices', [
            'invoices' => $invoices,
            'stats' => $stats
        ]);
    }

    /**
     * Smart view invoices - for both admin and regular users
     */
    public function viewInvoices()
    {
        $user = Auth::user();

        // Check user role and show appropriate view
        if ($user->role === 'admin') {
            // Admin sees all invoices
            $invoices = CouponInvoice::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_invoices' => CouponInvoice::count(),
                'total_amount' => CouponInvoice::sum('amount'),
                'completed_invoices' => CouponInvoice::where('payment_status', 'completed')->count(),
                'pending_invoices' => CouponInvoice::where('payment_status', 'pending')->count(),
                'failed_invoices' => CouponInvoice::where('payment_status', 'failed')->count(),
            ];

            return Inertia::render('admin/ViewInvoices', [
                'invoices' => $invoices,
                'stats' => $stats
            ]);
        } else {
            // Regular user sees only their invoices (combined)
            $combinedStats = $this->bookingInvoiceService->getCombinedInvoiceStats($user);
            
            // Get combined invoices with pagination
            $couponInvoices = CouponInvoice::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $bookingInvoices = BookingInvoice::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Combine and sort
            $allInvoices = $couponInvoices->concat($bookingInvoices)->sortByDesc('created_at');
            
            // Manual pagination
            $page = request()->get('page', 1);
            $perPage = 15;
            $offset = ($page - 1) * $perPage;
            $paginatedInvoices = $allInvoices->slice($offset, $perPage);
            
            $stats = [
                'total_invoices' => $combinedStats['total_invoices'],
                'total_amount' => $combinedStats['total_amount'],
                'completed_invoices' => $combinedStats['completed_invoices'],
                'pending_invoices' => $combinedStats['pending_invoices'],
                'failed_invoices' => $combinedStats['failed_invoices'],
                'booking_invoices' => $combinedStats['booking_invoices'],
                'coupon_invoices' => $combinedStats['coupon_invoices'],
            ];

            return Inertia::render('user/ViewInvoices', [
                'invoices' => $paginatedInvoices,
                'stats' => $stats,
                'user' => $user
            ]);
        }
    }
}
