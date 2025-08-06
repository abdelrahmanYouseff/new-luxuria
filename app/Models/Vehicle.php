<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'api_id',
        'plate_number',
        'status',
        'ownership_status',
        'make',
        'model',
        'year',
        'color',
        'category',
        'daily_rate',
        'weekly_rate',
        'monthly_rate',
        'transmission',
        'seats',
        'doors',
        'odometer',
        'description',
        'image',
        'is_visible'
    ];

    protected $appends = ['image_url'];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'weekly_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'seats' => 'integer',
        'doors' => 'integer',
        'odometer' => 'integer'
    ];

    /**
     * Get the image URL attribute
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }
        return asset('asset/image.png'); // Default image
    }

    /**
     * Get the full name attribute (make + model)
     */
    public function getFullNameAttribute()
    {
        return trim($this->make . ' ' . $this->model);
    }

    /**
     * Get the display name attribute
     */
    public function getDisplayNameAttribute()
    {
        return $this->full_name ?: 'Unknown Vehicle';
    }

    /**
     * Scope for available vehicles
     */
    public function scopeAvailable($query)
    {
        return $query->whereRaw('LOWER(status) = ?', ['available']);
    }

    /**
     * Scope for vehicles by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for vehicles by make
     */
    public function scopeByMake($query, $make)
    {
        return $query->where('make', $make);
    }

    /**
     * Scope for visible vehicles
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope for hidden vehicles
     */
    public function scopeHidden($query)
    {
        return $query->where('is_visible', false);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match(strtolower($this->status)) {
            'available' => 'bg-green-100 text-green-800',
            'rented' => 'bg-red-100 text-red-800',
            'maintenance' => 'bg-yellow-100 text-yellow-800',
            'out_of_service' => 'bg-gray-100 text-gray-800',
            'reserved' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get category badge class
     */
    public function getCategoryBadgeClassAttribute()
    {
        return match($this->category) {
            'economy' => 'bg-blue-100 text-blue-800',
            'luxury' => 'bg-purple-100 text-purple-800',
            'suv' => 'bg-green-100 text-green-800',
            'sports' => 'bg-red-100 text-red-800',
            'mid-range' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get ownership badge class
     */
    public function getOwnershipBadgeClassAttribute()
    {
        return match($this->ownership_status) {
            'owned' => 'bg-green-100 text-green-800',
            'leased' => 'bg-blue-100 text-blue-800',
            'rented' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
