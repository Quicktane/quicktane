<?php

declare(strict_types=1);

namespace App\Order\Models;

use App\Order\Enums\CreditMemoStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CreditMemo extends Model
{
    protected $table = 'credit_memos';

    protected $fillable = [
        'uuid',
        'order_id',
        'invoice_id',
        'increment_id',
        'status',
        'subtotal',
        'shipping_amount',
        'adjustment_positive',
        'adjustment_negative',
        'tax_amount',
        'grand_total',
    ];

    protected function casts(): array
    {
        return [
            'status' => CreditMemoStatus::class,
            'subtotal' => 'decimal:4',
            'shipping_amount' => 'decimal:4',
            'adjustment_positive' => 'decimal:4',
            'adjustment_negative' => 'decimal:4',
            'tax_amount' => 'decimal:4',
            'grand_total' => 'decimal:4',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (CreditMemo $creditMemo): void {
            if (empty($creditMemo->uuid)) {
                $creditMemo->uuid = (string) Str::uuid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CreditMemoItem::class);
    }
}
