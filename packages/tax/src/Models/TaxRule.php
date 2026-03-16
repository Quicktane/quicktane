<?php

declare(strict_types=1);

namespace Quicktane\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TaxRule extends Model
{
    protected $table = 'tax_rules';

    protected $fillable = [
        'uuid',
        'name',
        'tax_rate_id',
        'product_tax_class_id',
        'customer_tax_class_id',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (TaxRule $taxRule): void {
            if (empty($taxRule->uuid)) {
                $taxRule->uuid = (string) Str::uuid();
            }
        });
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function productTaxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class, 'product_tax_class_id');
    }

    public function customerTaxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class, 'customer_tax_class_id');
    }
}
