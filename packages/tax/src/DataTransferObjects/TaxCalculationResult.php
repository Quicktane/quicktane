<?php

declare(strict_types=1);

namespace Quicktane\Tax\DataTransferObjects;

class TaxCalculationResult
{
    /**
     * @param  array<string, string>  $breakdown
     */
    public function __construct(
        public readonly string $taxAmount,
        public readonly string $rate,
        public readonly array $breakdown,
    ) {}
}
