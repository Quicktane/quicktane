<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Contracts;

use Quicktane\Shipping\DataTransferObjects\ShippingRateOption;
use Quicktane\Shipping\DataTransferObjects\ShippingRateRequest;

interface ShippingCarrier
{
    public function code(): string;

    public function name(): string;

    /**
     * @return array<ShippingRateOption>
     */
    public function calculateRates(ShippingRateRequest $shippingRateRequest): array;

    public function isAvailable(ShippingRateRequest $shippingRateRequest): bool;
}
