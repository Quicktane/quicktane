<?php

declare(strict_types=1);

namespace App\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'order_item_id',
        'quantity',
        'row_total',
        'tax_amount',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'row_total' => 'decimal:4',
            'tax_amount' => 'decimal:4',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
