<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Quicktane\Promotion\Enums\ActionType;

class CartPriceRule extends Model
{
    protected $table = 'cart_price_rules';

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'is_active',
        'from_date',
        'to_date',
        'priority',
        'stop_further_processing',
        'action_type',
        'action_amount',
        'max_discount_amount',
        'apply_to_shipping',
        'times_used',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'from_date' => 'date',
            'to_date' => 'date',
            'priority' => 'integer',
            'stop_further_processing' => 'boolean',
            'action_type' => ActionType::class,
            'action_amount' => 'decimal:4',
            'max_discount_amount' => 'decimal:4',
            'apply_to_shipping' => 'boolean',
            'times_used' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (CartPriceRule $cartPriceRule): void {
            if (! $cartPriceRule->uuid) {
                $cartPriceRule->uuid = (string) Str::uuid();
            }
        });
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class, 'cart_price_rule_id');
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(RuleCondition::class, 'cart_price_rule_id');
    }

    public function appliedHistory(): HasMany
    {
        return $this->hasMany(RuleAppliedHistory::class, 'cart_price_rule_id');
    }
}
