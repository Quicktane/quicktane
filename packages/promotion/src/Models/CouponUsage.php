<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    public $timestamps = false;

    protected $table = 'coupon_usages';

    protected $fillable = [
        'coupon_id',
        'customer_id',
        'order_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
}
