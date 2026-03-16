<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Services;

use Quicktane\Shipping\Contracts\ShippingCarrier;
use Quicktane\Shipping\DataTransferObjects\ShippingRateOption;
use Quicktane\Shipping\DataTransferObjects\ShippingRateRequest;
use Quicktane\Shipping\Repositories\ShippingMethodRepository;

class ShippingRateService
{
    public function __construct(
        private readonly ShippingCarrierRegistry $shippingCarrierRegistry,
        private readonly ShippingMethodRepository $shippingMethodRepository,
    ) {}

    /**
     * @return array<ShippingCarrier>
     */
    public function getActiveCarriers(): array
    {
        return $this->shippingCarrierRegistry->getActiveCarriers();
    }

    /**
     * @return array<ShippingRateOption>
     */
    public function getAvailableRates(ShippingRateRequest $shippingRateRequest): array
    {
        $rateOptions = [];

        foreach ($this->shippingCarrierRegistry->getActiveCarriers() as $shippingCarrier) {
            if (! $shippingCarrier->isAvailable($shippingRateRequest)) {
                continue;
            }

            $carrierRates = $shippingCarrier->calculateRates($shippingRateRequest);

            foreach ($carrierRates as $rateOption) {
                $rateOptions[] = $rateOption;
            }
        }

        return $rateOptions;
    }

    public function resolveRate(string $carrierCode, string $methodCode, ShippingRateRequest $shippingRateRequest): ?ShippingRateOption
    {
        $shippingCarrier = $this->shippingCarrierRegistry->getCarrier($carrierCode);

        if ($shippingCarrier === null) {
            return null;
        }

        $rates = $shippingCarrier->calculateRates($shippingRateRequest);

        foreach ($rates as $rateOption) {
            if ($rateOption->methodCode === $methodCode) {
                return $rateOption;
            }
        }

        return null;
    }
}
