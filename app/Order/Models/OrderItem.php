<?php

declare(strict_types=1);

namespace App\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $fillable = [
        'uuid',
        'order_id',
        'product_id',
        'product_uuid',
        'product_type',
        'sku',
        'name',
        'quantity',
        'unit_price',
        'row_total',
        'discount_amount',
        'tax_amount',
        'tax_rate',
        'weight',
        'options',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:4',
            'row_total' => 'decimal:4',
            'discount_amount' => 'decimal:4',
            'tax_amount' => 'decimal:4',
            'tax_rate' => 'decimal:4',
            'weight' => 'decimal:4',
            'options' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (OrderItem $orderItem): void {
            if (empty($orderItem->uuid)) {
                $orderItem->uuid = (string) Str::uuid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
