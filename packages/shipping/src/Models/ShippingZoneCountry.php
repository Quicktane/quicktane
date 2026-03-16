<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Models;

use App\Directory\Models\Country;
use App\Directory\Models\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingZoneCountry extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'shipping_zone_id',
        'country_id',
        'region_id',
    ];

    protected function casts(): array
    {
        return [
            'shipping_zone_id' => 'integer',
            'country_id' => 'integer',
            'region_id' => 'integer',
        ];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
