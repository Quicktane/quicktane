<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Services\Evaluators;

use Quicktane\Promotion\Contracts\ConditionEvaluator;
use Quicktane\Promotion\DataTransferObjects\PromotionContext;
use Quicktane\Promotion\Models\RuleCondition;

class ProductAttributeEvaluator implements ConditionEvaluator
{
    public function evaluate(RuleCondition $condition, PromotionContext $context): bool
    {
        foreach ($context->cartItems as $cartItem) {
            $itemResult = $this->evaluateItem($condition, $cartItem);

            if ($itemResult) {
                $result = $condition->is_inverted ? false : true;

                return $result;
            }
        }

        return $condition->is_inverted;
    }

    /**
     * @param  array{product_id: int, sku: string, category_ids: int[], quantity: int, row_total: string, product_type: string}  $cartItem
     */
    private function evaluateItem(RuleCondition $condition, array $cartItem): bool
    {
        $attributeValue = $this->resolveItemAttribute($condition->attribute, $cartItem);

        if ($attributeValue === null) {
            return false;
        }

        return $this->compareValues($attributeValue, $condition->operator->value, $condition->value);
    }

    /**
     * @param  array{product_id: int, sku: string, category_ids: int[], quantity: int, row_total: string, product_type: string}  $cartItem
     */
    private function resolveItemAttribute(string $attribute, array $cartItem): string|array|null
    {
        return match ($attribute) {
            'sku' => $cartItem['sku'],
            'category_id' => $cartItem['category_ids'],
            'product_type' => $cartItem['product_type'],
            'quantity' => (string) $cartItem['quantity'],
            'row_total' => $cartItem['row_total'],
            default => null,
        };
    }

    private function compareValues(string|array $actualValue, string $operator, ?string $conditionValue): bool
    {
        if ($conditionValue === null) {
            return false;
        }

        if (is_array($actualValue)) {
            return $this->compareArrayValues($actualValue, $operator, $conditionValue);
        }

        return match ($operator) {
            '==' => $actualValue === $conditionValue,
            '!=' => $actualValue !== $conditionValue,
            '>' => bccomp($actualValue, $conditionValue, 4) > 0,
            '>=' => bccomp($actualValue, $conditionValue, 4) >= 0,
            '<' => bccomp($actualValue, $conditionValue, 4) < 0,
            '<=' => bccomp($actualValue, $conditionValue, 4) <= 0,
            'in' => in_array($actualValue, explode(',', $conditionValue), true),
            'not_in' => ! in_array($actualValue, explode(',', $conditionValue), true),
            'contains' => str_contains($actualValue, $conditionValue),
            default => false,
        };
    }

    /**
     * @param  array<int, int|string>  $actualValues
     */
    private function compareArrayValues(array $actualValues, string $operator, string $conditionValue): bool
    {
        $conditionValues = explode(',', $conditionValue);

        return match ($operator) {
            '==' => ! empty(array_intersect(array_map('strval', $actualValues), $conditionValues)),
            '!=' => empty(array_intersect(array_map('strval', $actualValues), $conditionValues)),
            'in' => ! empty(array_intersect(array_map('strval', $actualValues), $conditionValues)),
            'not_in' => empty(array_intersect(array_map('strval', $actualValues), $conditionValues)),
            default => false,
        };
    }
}
