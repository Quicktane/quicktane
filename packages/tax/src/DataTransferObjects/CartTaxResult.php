<?php

declare(strict_types=1);

namespace Quicktane\Tax\DataTransferObjects;

class CartTaxResult
{
    /**
     * @param  array<int, string>  $itemTaxes
     */
    public function __construct(
        public readonly string $totalTax,
        public readonly array $itemTaxes,
    ) {}
}
