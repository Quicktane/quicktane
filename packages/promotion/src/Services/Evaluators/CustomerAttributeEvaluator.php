<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Services\Evaluators;

use Quicktane\Promotion\Contracts\ConditionEvaluator;
use Quicktane\Promotion\DataTransferObjects\PromotionContext;
use Quicktane\Promotion\Models\RuleCondition;

class CustomerAttributeEvaluator implements ConditionEvaluator
{
    public function evaluate(RuleCondition $condition, PromotionContext $context): bool
    {
        $attributeValue = $this->resolveAttributeValue($condition->attribute, $context);

        if ($attributeValue === null) {
            return false;
        }

        $result = $this->compareValues((string) $attributeValue, $condition->operator->value, $condition->value);

        return $condition->is_inverted ? ! $result : $result;
    }

    private function resolveAttributeValue(string $attribute, PromotionContext $context): string|int|null
    {
        return match ($attribute) {
            'group_id' => $context->customerGroupId,
            'customer_id' => $context->customerId,
            default => null,
        };
    }

    private function compareValues(string $actualValue, string $operator, ?string $conditionValue): bool
    {
        if ($conditionValue === null) {
            return false;
        }

        return match ($operator) {
            '==' => $actualValue === $conditionValue,
            '!=' => $actualValue !== $conditionValue,
            'in' => in_array($actualValue, explode(',', $conditionValue), true),
            'not_in' => ! in_array($actualValue, explode(',', $conditionValue), true),
            default => false,
        };
    }
}
