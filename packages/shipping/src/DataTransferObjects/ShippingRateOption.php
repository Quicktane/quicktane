<?php

declare(strict_types=1);

namespace Quicktane\Shipping\DataTransferObjects;

class ShippingRateOption
{
    public function __construct(
        public readonly string $carrierCode,
        public readonly string $methodCode,
        public readonly string $label,
        public readonly string $price,
        public readonly ?int $estimatedDays,
    ) {}
}
