<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Contracts;

use Quicktane\Shipping\DataTransferObjects\ShippingRateOption;
use Quicktane\Shipping\DataTransferObjects\ShippingRateRequest;
use Quicktane\Shipping\Models\ShippingMethod;

interface ShippingFacade
{
    /**
     * @return array<ShippingRateOption>
     */
    public function getAvailableRates(ShippingRateRequest $shippingRateRequest): array;

    public function getMethod(string $code): ?ShippingMethod;

    /**
     * @return array<ShippingCarrier>
     */
    public function getActiveCarriers(): array;

    public function resolveRate(string $carrierCode, string $methodCode, ShippingRateRequest $shippingRateRequest): ?ShippingRateOption;
}
