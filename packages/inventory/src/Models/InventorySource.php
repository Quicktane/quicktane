<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class InventorySource extends Model
{
    protected $fillable = [
        'uuid',
        'code',
        'name',
        'description',
        'country_code',
        'city',
        'address',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'uuid' => 'string',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class, 'source_id');
    }

    protected static function booted(): void
    {
        static::creating(function (InventorySource $inventorySource): void {
            if (empty($inventorySource->uuid)) {
                $inventorySource->uuid = (string) Str::uuid();
            }
        });
    }
}
