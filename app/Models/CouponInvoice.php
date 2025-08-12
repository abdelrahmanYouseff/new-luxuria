<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'coupon_id',
        'coupon_name',
        'coupon_code',
        'amount',
        'currency',
        'stripe_payment_intent_id',
        'stripe_session_id',
        'payment_status',
        'payment_method',
        'customer_email',
        'customer_name',
        'payment_details',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the user that owns the invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');

        // Get the last invoice number for this month
        $lastInvoice = self::where('invoice_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract the sequence number and increment it
            $parts = explode('-', $lastInvoice->invoice_number);
            $sequence = (int) end($parts) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(array $paymentDetails = []): void
    {
        $this->update([
            'payment_status' => 'completed',
            'payment_details' => $paymentDetails,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark invoice as failed
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_details' => array_merge($this->payment_details ?? [], ['failure_reason' => $reason]),
        ]);
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if invoice is pending
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if invoice is failed
     */
    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            'completed' => 'badge bg-success',
            'pending' => 'badge bg-warning',
            'failed' => 'badge bg-danger',
            'refunded' => 'badge bg-info',
            default => 'badge bg-secondary',
        };
    }
}
