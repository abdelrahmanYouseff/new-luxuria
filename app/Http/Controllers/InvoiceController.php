<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CouponInvoiceService;
use Inertia\Inertia;
use App\Models\CouponInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    private CouponInvoiceService $couponInvoiceService;

    public function __construct(CouponInvoiceService $couponInvoiceService)
    {
        $this->couponInvoiceService = $couponInvoiceService;
    }

    /**
     * Display the invoices index page
     */
    public function index()
    {
        return view('invoices.index');
    }

    /**
     * Get user's invoices via API
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
                ],
            ]);
        }

        $invoices = $this->couponInvoiceService->getUserInvoices($user, 50);
        $stats = $this->couponInvoiceService->getInvoiceStats($user);

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
            // Regular user sees only their invoices
            $invoices = CouponInvoice::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

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
}
