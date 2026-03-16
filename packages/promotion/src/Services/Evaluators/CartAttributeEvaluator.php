<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Services\Evaluators;

use Quicktane\Promotion\Contracts\ConditionEvaluator;
use Quicktane\Promotion\DataTransferObjects\PromotionContext;
use Quicktane\Promotion\Models\RuleCondition;

class CartAttributeEvaluator implements ConditionEvaluator
{
    public function evaluate(RuleCondition $condition, PromotionContext $context): bool
    {
        $attributeValue = $this->resolveAttributeValue($condition->attribute, $context);

        if ($attributeValue === null) {
            return false;
        }

        $result = $this->compareValues($attributeValue, $condition->operator->value, $condition->value);

        return $condition->is_inverted ? ! $result : $result;
    }

    private function resolveAttributeValue(string $attribute, PromotionContext $context): string|int|null
    {
        return match ($attribute) {
            'subtotal' => $context->subtotal,
            'items_count' => $context->itemsCount,
            'total_weight' => $context->totalWeight,
            default => null,
        };
    }

    private function compareValues(string|int $actualValue, string $operator, ?string $conditionValue): bool
    {
        if ($conditionValue === null) {
            return false;
        }

        return match ($operator) {
            '==' => bccomp((string) $actualValue, $conditionValue, 4) === 0,
            '!=' => bccomp((string) $actualValue, $conditionValue, 4) !== 0,
            '>' => bccomp((string) $actualValue, $conditionValue, 4) > 0,
            '>=' => bccomp((string) $actualValue, $conditionValue, 4) >= 0,
            '<' => bccomp((string) $actualValue, $conditionValue, 4) < 0,
            '<=' => bccomp((string) $actualValue, $conditionValue, 4) <= 0,
            default => false,
        };
    }
}
