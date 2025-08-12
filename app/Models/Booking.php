<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'emirate',
        'start_date',
        'end_date',
        'status',
        'daily_rate',
        'pricing_type',
        'applied_rate',
        'total_days',
        'total_amount',
        'notes',
        'stripe_session_id',
        'external_reservation_id',
        'external_reservation_uid',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_rate' => 'decimal:2',
        'applied_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vehicle that is booked.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get formatted pricing type
     */
    public function getFormattedPricingTypeAttribute(): string
    {
        $types = [
            'daily' => 'Daily Rate',
            'weekly' => 'Weekly Rate (per day)',
            'monthly' => 'Monthly Rate (per day)',
        ];

        return $types[$this->pricing_type] ?? 'Daily Rate';
    }

    /**
     * Check if booking is active (overlaps with given dates)
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if booking conflicts with given dates
     */
    public static function hasConflict($vehicleId, $startDate, $endDate, $excludeBookingId = null)
    {
        $query = self::where('vehicle_id', $vehicleId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
            default => 'Unknown',
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning text-dark',
            'confirmed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'completed' => 'bg-info',
            default => 'bg-secondary',
        };
    }
}
