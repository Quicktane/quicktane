<?php

declare(strict_types=1);

namespace Quicktane\Tax\Models;

use App\Directory\Models\Country;
use App\Directory\Models\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxZoneRule extends Model
{
    protected $table = 'tax_zone_rules';

    protected $fillable = [
        'tax_zone_id',
        'country_id',
        'region_id',
        'postcode_from',
        'postcode_to',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(TaxZone::class, 'tax_zone_id');
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
