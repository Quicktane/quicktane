<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShippingRate extends Model
{
    protected $fillable = [
        'uuid',
        'shipping_method_id',
        'shipping_zone_id',
        'price',
        'min_weight',
        'max_weight',
        'min_subtotal',
        'max_subtotal',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:4',
            'min_weight' => 'decimal:4',
            'max_weight' => 'decimal:4',
            'min_subtotal' => 'decimal:4',
            'max_subtotal' => 'decimal:4',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    protected static function booted(): void
    {
        static::creating(function (ShippingRate $shippingRate): void {
            if (empty($shippingRate->uuid)) {
                $shippingRate->uuid = (string) Str::uuid();
            }
        });
    }
}
