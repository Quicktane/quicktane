<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ShippingMethod extends Model
{
    protected $fillable = [
        'uuid',
        'code',
        'name',
        'carrier_code',
        'description',
        'is_active',
        'sort_order',
        'min_order_amount',
        'max_order_amount',
        'free_shipping_threshold',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'config' => 'array',
            'min_order_amount' => 'decimal:4',
            'max_order_amount' => 'decimal:4',
            'free_shipping_threshold' => 'decimal:4',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function rates(): HasMany
    {
        return $this->hasMany(ShippingRate::class);
    }

    protected static function booted(): void
    {
        static::creating(function (ShippingMethod $shippingMethod): void {
            if (empty($shippingMethod->uuid)) {
                $shippingMethod->uuid = (string) Str::uuid();
            }
        });
    }
}
