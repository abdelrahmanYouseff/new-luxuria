<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponWebsite extends Model
{
    protected $table = 'coupon_website'; // Explicitly set table name

    protected $fillable = [
        'code',
        'discount',
        'discount_type',
        'expire_at',
    ];

    protected $casts = [
        'expire_at' => 'datetime',
        'discount' => 'decimal:2',
    ];
}
