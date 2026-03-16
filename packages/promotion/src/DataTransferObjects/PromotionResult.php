<?php

declare(strict_types=1);

namespace Quicktane\Promotion\DataTransferObjects;

class PromotionResult
{
    /**
     * @param  DiscountResult[]  $discounts
     */
    public function __construct(
        public readonly string $totalDiscount,
        public readonly array $discounts,
        public readonly bool $freeShipping,
    ) {}
}
