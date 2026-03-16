<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public $timestamps = false;

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'source_id',
        'quantity_change',
        'reason',
        'reference_type',
        'reference_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'source_id' => 'integer',
            'quantity_change' => 'integer',
            'user_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(InventorySource::class, 'source_id');
    }

    protected static function booted(): void
    {
        static::creating(function (StockMovement $stockMovement): void {
            if ($stockMovement->created_at === null) {
                $stockMovement->created_at = now();
            }
        });
    }
}
