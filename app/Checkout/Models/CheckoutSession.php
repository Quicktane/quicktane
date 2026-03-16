<?php

declare(strict_types=1);

namespace App\Checkout\Models;

use App\Cart\Models\Cart;
use App\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CheckoutSession extends Model
{
    protected $table = 'checkout_sessions';

    protected $fillable = [
        'uuid',
        'cart_id',
        'customer_id',
        'shipping_address',
        'billing_address',
        'shipping_method_code',
        'shipping_method_label',
        'shipping_amount',
        'payment_method_code',
        'coupon_code',
        'totals',
        'step',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'shipping_amount' => 'decimal:4',
            'totals' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (CheckoutSession $checkoutSession): void {
            if (empty($checkoutSession->uuid)) {
                $checkoutSession->uuid = (string) Str::uuid();
            }
        });
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
