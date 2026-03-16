<?php

declare(strict_types=1);

namespace App\Cart\Models;

use App\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Cart extends Model
{
    protected $table = 'carts';

    protected $fillable = [
        'customer_id',
        'store_id',
        'guest_token',
        'status',
        'currency_code',
        'items_count',
        'subtotal',
        'ip_address',
        'user_agent',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CartStatus::class,
            'items_count' => 'integer',
            'subtotal' => 'decimal:4',
            'converted_at' => 'datetime',
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

        static::creating(function (Cart $cart): void {
            if (! $cart->uuid) {
                $cart->uuid = (string) Str::uuid();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
