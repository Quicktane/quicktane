<?php

declare(strict_types=1);

namespace App\Cart\DataTransferObjects;

class CartTotals
{
    public function __construct(
        public readonly string $subtotal,
        public readonly int $itemsCount,
        public readonly array $items,
    ) {}
}
