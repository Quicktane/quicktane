<?php

declare(strict_types=1);

namespace Quicktane\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TaxZone extends Model
{
    protected $table = 'tax_zones';

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (TaxZone $taxZone): void {
            if (empty($taxZone->uuid)) {
                $taxZone->uuid = (string) Str::uuid();
            }
        });
    }

    public function zoneRules(): HasMany
    {
        return $this->hasMany(TaxZoneRule::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }
}
