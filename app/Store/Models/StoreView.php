<?php

declare(strict_types=1);

namespace App\Store\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class StoreView extends Model
{
    protected $table = 'store_views';

    protected $fillable = [
        'uuid',
        'store_id',
        'code',
        'name',
        'locale',
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

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    protected static function booted(): void
    {
        static::creating(function (StoreView $storeView): void {
            if (empty($storeView->uuid)) {
                $storeView->uuid = (string) Str::uuid();
            }
        });
    }
}
