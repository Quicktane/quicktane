<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Quicktane\Promotion\Enums\ConditionAggregator;
use Quicktane\Promotion\Enums\ConditionOperator;
use Quicktane\Promotion\Enums\ConditionType;

class RuleCondition extends Model
{
    protected $table = 'rule_conditions';

    protected $fillable = [
        'cart_price_rule_id',
        'parent_id',
        'type',
        'attribute',
        'operator',
        'value',
        'aggregator',
        'is_inverted',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => ConditionType::class,
            'operator' => ConditionOperator::class,
            'aggregator' => ConditionAggregator::class,
            'is_inverted' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(CartPriceRule::class, 'cart_price_rule_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
