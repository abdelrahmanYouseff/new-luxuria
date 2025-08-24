<?php

namespace App\Services;

use App\Models\BookingInvoice;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BookingInvoiceService
{
    /**
     * Create a new booking invoice
     */
    public function createInvoice(Booking $booking): BookingInvoice
    {
        try {
            $invoice = BookingInvoice::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'invoice_number' => BookingInvoice::generateInvoiceNumber(),
                'vehicle_make' => $booking->vehicle->make,
                'vehicle_model' => $booking->vehicle->model,
                'vehicle_year' => $booking->vehicle->year ?? null,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
                'total_days' => $booking->total_days,
                'daily_rate' => $booking->daily_rate,
                'applied_rate' => $booking->applied_rate,
                'total_amount' => $booking->total_amount,
                'currency' => 'AED',
                'payment_status' => 'pending',
                'emirate' => $booking->emirate,
                'notes' => $booking->notes,
            ]);

            Log::info('Booking invoice created', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'amount' => $invoice->total_amount,
            ]);

            return $invoice;
        } catch (\Exception $e) {
            Log::error('Error creating booking invoice: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
            ]);
            throw $e;
        }
    }

    /**
     * Update invoice payment status
     */
    public function updatePaymentStatus(string $stripeSessionId, string $status, array $paymentDetails = []): ?BookingInvoice
    {
        try {
            $invoice = BookingInvoice::where('stripe_session_id', $stripeSessionId)->first();

            if (!$invoice) {
                Log::warning('Booking invoice not found for stripe session', [
                    'stripe_session_id' => $stripeSessionId,
                    'status' => $status,
                ]);
                return null;
            }

            if ($status === 'completed') {
                $invoice->markAsPaid($paymentDetails);
            } elseif ($status === 'failed') {
                $invoice->markAsFailed($paymentDetails['failure_reason'] ?? 'Payment failed');
            } else {
                $invoice->update([
                    'payment_status' => $status,
                    'payment_details' => $paymentDetails,
                ]);
            }

            Log::info('Booking invoice payment status updated', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $status,
                'stripe_session_id' => $stripeSessionId,
            ]);

            return $invoice;
        } catch (\Exception $e) {
            Log::error('Error updating booking invoice payment status: ' . $e->getMessage(), [
                'stripe_session_id' => $stripeSessionId,
                'status' => $status,
            ]);
            return null;
        }
    }

    /**
     * Get user's booking invoices
     */
    public function getUserInvoices(User $user, int $limit = 50): array
    {
        try {
            $invoices = $user->bookingInvoices()
                ->with('booking')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'booking_id' => $invoice->booking_id,
                        'vehicle_name' => $invoice->vehicle_full_name,
                        'start_date' => $invoice->start_date->format('d/m/Y'),
                        'end_date' => $invoice->end_date->format('d/m/Y'),
                        'total_days' => $invoice->total_days,
                        'total_amount' => $invoice->total_amount,
                        'currency' => $invoice->currency,
                        'payment_status' => $invoice->payment_status,
                        'formatted_payment_status' => $invoice->formatted_payment_status,
                        'payment_status_badge_class' => $invoice->payment_status_badge_class,
                        'payment_method' => $invoice->payment_method,
                        'emirate' => $invoice->emirate,
                        'created_at' => $invoice->created_at->format('d/m/Y H:i'),
                        'paid_at' => $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : null,
                        'type' => 'booking',
                    ];
                })
                ->toArray();

            return $invoices;
        } catch (\Exception $e) {
            Log::error('Error getting user booking invoices: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
            return [];
        }
    }

    /**
     * Get invoice statistics for user
     */
    public function getInvoiceStats(User $user): array
    {
        try {
            $invoices = $user->bookingInvoices();

            return [
                'total_invoices' => $invoices->count(),
                'total_amount' => $invoices->sum('total_amount'),
                'completed_invoices' => $invoices->where('payment_status', 'completed')->count(),
                'pending_invoices' => $invoices->where('payment_status', 'pending')->count(),
                'failed_invoices' => $invoices->where('payment_status', 'failed')->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting booking invoice stats: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
            return [
                'total_invoices' => 0,
                'total_amount' => 0,
                'completed_invoices' => 0,
                'pending_invoices' => 0,
                'failed_invoices' => 0,
            ];
        }
    }

    /**
     * Get combined invoice statistics (coupon + booking)
     */
    public function getCombinedInvoiceStats(User $user): array
    {
        try {
            $bookingStats = $this->getInvoiceStats($user);
            
            // Get coupon invoice stats
            $couponInvoices = $user->couponInvoices();
            $couponStats = [
                'total_invoices' => $couponInvoices->count(),
                'total_amount' => $couponInvoices->sum('amount'),
                'completed_invoices' => $couponInvoices->where('payment_status', 'completed')->count(),
                'pending_invoices' => $couponInvoices->where('payment_status', 'pending')->count(),
                'failed_invoices' => $couponInvoices->where('payment_status', 'failed')->count(),
            ];

            // Combine stats
            return [
                'total_invoices' => $bookingStats['total_invoices'] + $couponStats['total_invoices'],
                'total_amount' => $bookingStats['total_amount'] + $couponStats['total_amount'],
                'completed_invoices' => $bookingStats['completed_invoices'] + $couponStats['completed_invoices'],
                'pending_invoices' => $bookingStats['pending_invoices'] + $couponStats['pending_invoices'],
                'failed_invoices' => $bookingStats['failed_invoices'] + $couponStats['failed_invoices'],
                'booking_invoices' => $bookingStats['total_invoices'],
                'coupon_invoices' => $couponStats['total_invoices'],
            ];
        } catch (\Exception $e) {
            Log::error('Error getting combined invoice stats: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
            return [
                'total_invoices' => 0,
                'total_amount' => 0,
                'completed_invoices' => 0,
                'pending_invoices' => 0,
                'failed_invoices' => 0,
                'booking_invoices' => 0,
                'coupon_invoices' => 0,
            ];
        }
    }

    /**
     * Get combined user invoices (coupon + booking)
     */
    public function getCombinedUserInvoices(User $user, int $limit = 50): array
    {
        try {
            // Get booking invoices
            $bookingInvoices = $this->getUserInvoices($user, $limit);

            // Get coupon invoices
            $couponInvoices = $user->couponInvoices()
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'coupon_name' => $invoice->coupon_name,
                        'coupon_code' => $invoice->coupon_code,
                        'total_amount' => $invoice->amount,
                        'currency' => $invoice->currency,
                        'payment_status' => $invoice->payment_status,
                        'formatted_payment_status' => ucfirst($invoice->payment_status),
                        'payment_status_badge_class' => $this->getCouponInvoiceStatusBadgeClass($invoice->payment_status),
                        'payment_method' => $invoice->payment_method,
                        'created_at' => $invoice->created_at->format('d/m/Y H:i'),
                        'paid_at' => $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : null,
                        'type' => 'coupon',
                    ];
                })
                ->toArray();

            // Combine and sort by created_at
            $allInvoices = array_merge($bookingInvoices, $couponInvoices);
            usort($allInvoices, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return array_slice($allInvoices, 0, $limit);
        } catch (\Exception $e) {
            Log::error('Error getting combined user invoices: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
            return [];
        }
    }

    /**
     * Get coupon invoice status badge class
     */
    private function getCouponInvoiceStatusBadgeClass(string $status): string
    {
        return match($status) {
            'pending' => 'bg-warning text-dark',
            'completed' => 'bg-success',
            'failed' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }
}
