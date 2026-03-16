<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Services;

use Quicktane\Promotion\Contracts\ConditionEvaluator;
use Quicktane\Promotion\DataTransferObjects\PromotionContext;
use Quicktane\Promotion\Enums\ConditionType;
use Quicktane\Promotion\Models\RuleCondition;
use Quicktane\Promotion\Services\Evaluators\CartAttributeEvaluator;
use Quicktane\Promotion\Services\Evaluators\CombineEvaluator;
use Quicktane\Promotion\Services\Evaluators\CustomerAttributeEvaluator;
use Quicktane\Promotion\Services\Evaluators\ProductAttributeEvaluator;

class ConditionEngine
{
    public function __construct(
        private readonly CombineEvaluator $combineEvaluator,
        private readonly CartAttributeEvaluator $cartAttributeEvaluator,
        private readonly ProductAttributeEvaluator $productAttributeEvaluator,
        private readonly CustomerAttributeEvaluator $customerAttributeEvaluator,
    ) {}

    public function evaluate(RuleCondition $rootCondition, PromotionContext $context): bool
    {
        $evaluator = $this->resolveEvaluator($rootCondition->type);

        return $evaluator->evaluate($rootCondition, $context);
    }

    private function resolveEvaluator(ConditionType $conditionType): ConditionEvaluator
    {
        return match ($conditionType) {
            ConditionType::Combine => $this->combineEvaluator,
            ConditionType::CartAttribute => $this->cartAttributeEvaluator,
            ConditionType::ProductAttribute => $this->productAttributeEvaluator,
            ConditionType::CustomerAttribute => $this->customerAttributeEvaluator,
        };
    }
}
