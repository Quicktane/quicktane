<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleAppliedHistory extends Model
{
    public $timestamps = false;

    protected $table = 'rule_applied_history';

    protected $fillable = [
        'cart_price_rule_id',
        'cart_id',
        'order_id',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:4',
            'created_at' => 'datetime',
        ];
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(CartPriceRule::class, 'cart_price_rule_id');
    }
}
