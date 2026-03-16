<?php

declare(strict_types=1);

namespace Quicktane\Shipping\DataTransferObjects;

class ShippingRateRequest
{
    public function __construct(
        public readonly array $items,
        public readonly array $shippingAddress,
        public readonly string $subtotal,
        public readonly ?string $totalWeight,
        public readonly string $currencyCode,
    ) {}
}
