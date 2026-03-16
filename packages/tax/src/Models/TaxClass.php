<?php

declare(strict_types=1);

namespace Quicktane\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Quicktane\Tax\Enums\TaxClassType;

class TaxClass extends Model
{
    protected $table = 'tax_classes';

    protected $fillable = [
        'uuid',
        'name',
        'type',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'type' => TaxClassType::class,
            'is_default' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (TaxClass $taxClass): void {
            if (empty($taxClass->uuid)) {
                $taxClass->uuid = (string) Str::uuid();
            }
        });
    }
}
