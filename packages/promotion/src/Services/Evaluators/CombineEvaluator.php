<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Services\Evaluators;

use Quicktane\Promotion\Contracts\ConditionEvaluator;
use Quicktane\Promotion\DataTransferObjects\PromotionContext;
use Quicktane\Promotion\Enums\ConditionAggregator;
use Quicktane\Promotion\Models\RuleCondition;
use Quicktane\Promotion\Services\ConditionEngine;

class CombineEvaluator implements ConditionEvaluator
{
    public function evaluate(RuleCondition $condition, PromotionContext $context): bool
    {
        $conditionEngine = app()->make(ConditionEngine::class);

        $children = $condition->children()->orderBy('sort_order')->get();

        if ($children->isEmpty()) {
            return true;
        }

        $aggregator = $condition->aggregator ?? ConditionAggregator::All;

        if ($aggregator === ConditionAggregator::All) {
            foreach ($children as $childCondition) {
                $childResult = $conditionEngine->evaluate($childCondition, $context);

                if (! $childResult) {
                    return false;
                }
            }

            return true;
        }

        foreach ($children as $childCondition) {
            $childResult = $conditionEngine->evaluate($childCondition, $context);

            if ($childResult) {
                return true;
            }
        }

        return false;
    }
}
