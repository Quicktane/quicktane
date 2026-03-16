<?php

declare(strict_types=1);

namespace App\Store\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Store extends Model
{
    protected $table = 'stores';

    protected $fillable = [
        'uuid',
        'website_id',
        'code',
        'name',
        'root_category_id',
        'sort_order',
        'is_active',
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

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function storeViews(): HasMany
    {
        return $this->hasMany(StoreView::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Store $store): void {
            if (empty($store->uuid)) {
                $store->uuid = (string) Str::uuid();
            }
        });
    }
}
