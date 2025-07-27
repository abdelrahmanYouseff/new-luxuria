<?php

namespace App\Services;

use App\Models\CouponInvoice;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmationMail;

class CouponInvoiceService
{
    /**
     * Create a new coupon invoice
     */
    public function createInvoice(array $data): CouponInvoice
    {
        try {
            $invoice = CouponInvoice::create([
                'user_id' => $data['user_id'],
                'invoice_number' => CouponInvoice::generateInvoiceNumber(),
                'coupon_id' => $data['coupon_id'],
                'coupon_name' => $data['coupon_name'],
                'coupon_code' => $data['coupon_code'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'AED',
                'stripe_payment_intent_id' => $data['stripe_payment_intent_id'],
                'stripe_session_id' => $data['stripe_session_id'] ?? null,
                'payment_status' => 'pending',
                'customer_email' => $data['customer_email'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
            ]);

            Log::info('Coupon invoice created', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'user_id' => $invoice->user_id,
                'coupon_id' => $invoice->coupon_id,
                'amount' => $invoice->amount,
            ]);

            return $invoice;
        } catch (\Exception $e) {
            Log::error('Error creating coupon invoice: ' . $e->getMessage(), $data);
            throw $e;
        }
    }

    /**
     * Update invoice payment status
     */
    public function updatePaymentStatus(string $paymentIntentId, string $status, array $paymentDetails = []): ?CouponInvoice
    {
        try {
            $invoice = CouponInvoice::where('stripe_payment_intent_id', $paymentIntentId)->first();

            if (!$invoice) {
                Log::warning('Invoice not found for payment intent', ['payment_intent_id' => $paymentIntentId]);
                return null;
            }

            switch ($status) {
                case 'succeeded':
                    $invoice->markAsPaid($paymentDetails);
                    break;
                case 'failed':
                    $invoice->markAsFailed($paymentDetails['failure_reason'] ?? 'Payment failed');
                    break;
                case 'canceled':
                    $invoice->markAsFailed('Payment canceled');
                    break;
                default:
                    $invoice->update(['payment_status' => $status]);
            }

            Log::info('Invoice payment status updated', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $status,
            ]);

            return $invoice;
        } catch (\Exception $e) {
            Log::error('Error updating invoice payment status: ' . $e->getMessage(), [
                'payment_intent_id' => $paymentIntentId,
                'status' => $status,
            ]);
            throw $e;
        }
    }

    /**
     * Get invoice by payment intent ID
     */
    public function getInvoiceByPaymentIntent(string $paymentIntentId): ?CouponInvoice
    {
        return CouponInvoice::where('stripe_payment_intent_id', $paymentIntentId)->first();
    }

    /**
     * Get user's invoices
     */
    public function getUserInvoices(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $user->couponInvoices()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get invoice statistics
     */
    public function getInvoiceStats(User $user = null): array
    {
        $query = CouponInvoice::query();

        if ($user) {
            $query->where('user_id', $user->id);
        }

        return [
            'total_invoices' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'completed_invoices' => $query->where('payment_status', 'completed')->count(),
            'pending_invoices' => $query->where('payment_status', 'pending')->count(),
            'failed_invoices' => $query->where('payment_status', 'failed')->count(),
        ];
    }

    /**
     * Process successful payment
     */
    public function processSuccessfulPayment(string $paymentIntentId, array $paymentDetails = []): ?CouponInvoice
    {
        $invoice = $this->updatePaymentStatus($paymentIntentId, 'succeeded', $paymentDetails);

        if ($invoice) {
            // Send confirmation email
            $this->sendPaymentConfirmationEmail($invoice);

            // Here you can add additional logic like:
            // - Adding points to user account
            // - Updating coupon usage
            // - Sending notification

            Log::info('Payment processed successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->amount,
            ]);
        }

        return $invoice;
    }

    /**
     * Process failed payment
     */
    public function processFailedPayment(string $paymentIntentId, string $reason = 'Payment failed'): ?CouponInvoice
    {
        $invoice = $this->updatePaymentStatus($paymentIntentId, 'failed', ['failure_reason' => $reason]);

        if ($invoice) {
            Log::info('Payment failed', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'reason' => $reason,
            ]);
        }

        return $invoice;
    }

    /**
     * Send payment confirmation email
     */
    private function sendPaymentConfirmationEmail(CouponInvoice $invoice): void
    {
        try {
            // Generate coupon code if not provided
            $couponDetails = [
                'code' => 'LUXURIA' . strtoupper(substr($invoice->invoice_number, -6)),
                'discount_type' => 'percentage', // You can get this from coupon API
                'discount_value' => '10', // You can get this from coupon API
            ];

            // Send email to customer
            if ($invoice->customer_email) {
                Mail::to($invoice->customer_email)->queue(
                    new PaymentConfirmationMail($invoice, $couponDetails)
                );

                Log::info('Payment confirmation email queued', [
                    'invoice_id' => $invoice->id,
                    'email' => $invoice->customer_email,
                    'invoice_number' => $invoice->invoice_number,
                ]);
            } else {
                Log::warning('No email address found for invoice', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
