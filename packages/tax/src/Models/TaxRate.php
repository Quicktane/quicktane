<?php

declare(strict_types=1);

namespace Quicktane\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TaxRate extends Model
{
    protected $table = 'tax_rates';

    protected $fillable = [
        'uuid',
        'name',
        'tax_zone_id',
        'rate',
        'priority',
        'is_compound',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'priority' => 'integer',
            'is_compound' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (TaxRate $taxRate): void {
            if (empty($taxRate->uuid)) {
                $taxRate->uuid = (string) Str::uuid();
            }
        });
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(TaxZone::class, 'tax_zone_id');
    }
}
