<?php

declare(strict_types=1);

namespace Quicktane\Promotion\DataTransferObjects;

use Quicktane\Promotion\Enums\ActionType;

class DiscountResult
{
    public function __construct(
        public readonly int $ruleId,
        public readonly string $ruleName,
        public readonly string $discountAmount,
        public readonly ActionType $actionType,
        public readonly ?string $couponCode,
    ) {}
}
