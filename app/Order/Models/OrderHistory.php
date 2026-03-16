<?php

declare(strict_types=1);

namespace App\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderHistory extends Model
{
    public $timestamps = false;

    protected $table = 'order_history';

    protected $fillable = [
        'order_id',
        'status',
        'comment',
        'is_customer_notified',
        'user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_customer_notified' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
