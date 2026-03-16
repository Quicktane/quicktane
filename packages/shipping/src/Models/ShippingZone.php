<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ShippingZone extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function countries(): HasMany
    {
        return $this->hasMany(ShippingZoneCountry::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(ShippingRate::class);
    }

    protected static function booted(): void
    {
        static::creating(function (ShippingZone $shippingZone): void {
            if (empty($shippingZone->uuid)) {
                $shippingZone->uuid = (string) Str::uuid();
            }
        });
    }
}
