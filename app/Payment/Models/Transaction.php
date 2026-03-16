<?php

declare(strict_types=1);

namespace App\Payment\Models;

use App\Payment\Enums\TransactionStatus;
use App\Payment\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'uuid',
        'order_id',
        'payment_method_code',
        'type',
        'status',
        'amount',
        'currency_code',
        'reference_id',
        'parent_transaction_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
            'amount' => 'decimal:4',
            'metadata' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Transaction $transaction): void {
            if (! $transaction->uuid) {
                $transaction->uuid = (string) Str::uuid();
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'parent_transaction_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Transaction::class, 'parent_transaction_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TransactionLog::class);
    }
}
