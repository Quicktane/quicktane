<?php

declare(strict_types=1);

namespace App\Cart\DataTransferObjects;

class PriceValidationResult
{
    public function __construct(
        public readonly bool $isValid,
        public readonly array $changedItems,
        public readonly array $outOfStockItems,
        public readonly array $insufficientStockItems,
    ) {}
}
