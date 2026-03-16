<?php

declare(strict_types=1);

namespace App\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'gateway_code',
        'description',
        'is_active',
        'sort_order',
        'min_order_amount',
        'max_order_amount',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'config' => 'array',
            'min_order_amount' => 'decimal:4',
            'max_order_amount' => 'decimal:4',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (PaymentMethod $paymentMethod): void {
            if (! $paymentMethod->uuid) {
                $paymentMethod->uuid = (string) Str::uuid();
            }
        });
    }
}
