<?php

declare(strict_types=1);

namespace App\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CartItem extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_uuid',
        'product_type',
        'sku',
        'name',
        'quantity',
        'unit_price',
        'row_total',
        'options',
        'snapshotted_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:4',
            'row_total' => 'decimal:4',
            'options' => 'array',
            'snapshotted_at' => 'datetime',
            'uuid' => 'string',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (CartItem $item): void {
            if (! $item->uuid) {
                $item->uuid = (string) Str::uuid();
            }
        });
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
