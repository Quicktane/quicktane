<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Contracts;

use Quicktane\Promotion\DataTransferObjects\PromotionContext;
use Quicktane\Promotion\Models\RuleCondition;

interface ConditionEvaluator
{
    public function evaluate(RuleCondition $condition, PromotionContext $context): bool;
}
