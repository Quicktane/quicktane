<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Models;

use App\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockItem extends Model
{
    protected $fillable = [
        'product_id',
        'source_id',
        'quantity',
        'reserved',
        'notify_quantity',
        'is_in_stock',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'source_id' => 'integer',
            'quantity' => 'integer',
            'reserved' => 'integer',
            'notify_quantity' => 'integer',
            'is_in_stock' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(InventorySource::class, 'source_id');
    }
}
