<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'uuid',
        'cart_price_rule_id',
        'code',
        'usage_limit',
        'usage_per_customer',
        'times_used',
        'is_active',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'usage_limit' => 'integer',
            'usage_per_customer' => 'integer',
            'times_used' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (Coupon $coupon): void {
            if (! $coupon->uuid) {
                $coupon->uuid = (string) Str::uuid();
            }
        });
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(CartPriceRule::class, 'cart_price_rule_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id');
    }
}
