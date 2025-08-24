<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'invoice_number',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'start_date',
        'end_date',
        'total_days',
        'daily_rate',
        'applied_rate',
        'total_amount',
        'currency',
        'payment_status',
        'payment_method',
        'stripe_session_id',
        'payment_details',
        'paid_at',
        'emirate',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_rate' => 'decimal:2',
        'applied_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
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
     * Get the booking that owns the invoice.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Generate a unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'BK-INV';
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
    public function markAsFailed(string $reason = ''): void
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_details' => array_merge($this->payment_details ?? [], ['failure_reason' => $reason]),
        ]);
    }

    /**
     * Get formatted payment status
     */
    public function getFormattedPaymentStatusAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    /**
     * Get payment status badge class
     */
    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'bg-warning text-dark',
            'completed' => 'bg-success',
            'failed' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get vehicle full name
     */
    public function getVehicleFullNameAttribute(): string
    {
        $name = $this->vehicle_make . ' ' . $this->vehicle_model;
        if ($this->vehicle_year) {
            $name .= ' (' . $this->vehicle_year . ')';
        }
        return $name;
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 2) . ' ' . $this->currency;
    }

    /**
     * Get formatted daily rate
     */
    public function getFormattedDailyRateAttribute(): string
    {
        return number_format($this->daily_rate, 2) . ' ' . $this->currency;
    }

    /**
     * Get formatted applied rate
     */
    public function getFormattedAppliedRateAttribute(): string
    {
        return number_format($this->applied_rate, 2) . ' ' . $this->currency;
    }
}
