<?php

declare(strict_types=1);

namespace App\Order\Models;

use App\Order\Enums\OrderAddressType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    protected $table = 'order_addresses';

    protected $fillable = [
        'order_id',
        'type',
        'first_name',
        'last_name',
        'company',
        'street_line_1',
        'street_line_2',
        'city',
        'region_id',
        'region_name',
        'postcode',
        'country_id',
        'country_name',
        'phone',
    ];

    protected function casts(): array
    {
        return [
            'type' => OrderAddressType::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
