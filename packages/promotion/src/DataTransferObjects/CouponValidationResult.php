<?php

declare(strict_types=1);

namespace Quicktane\Promotion\DataTransferObjects;

use Quicktane\Promotion\Models\CartPriceRule;

class CouponValidationResult
{
    public function __construct(
        public readonly bool $isValid,
        public readonly ?string $errorMessage,
        public readonly ?CartPriceRule $rule,
    ) {}
}
