<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Services;

use Quicktane\Shipping\Contracts\ShippingCarrier;
use Quicktane\Shipping\DataTransferObjects\ShippingRateOption;
use Quicktane\Shipping\DataTransferObjects\ShippingRateRequest;
use Quicktane\Shipping\Models\ShippingRate;
use Quicktane\Shipping\Repositories\ShippingMethodRepository;
use Quicktane\Shipping\Repositories\ShippingRateRepository;
use Quicktane\Shipping\Repositories\ShippingZoneRepository;

class FlatRateCarrier implements ShippingCarrier
{
    public function __construct(
        private readonly ShippingMethodRepository $shippingMethodRepository,
        private readonly ShippingRateRepository $shippingRateRepository,
        private readonly ShippingZoneRepository $shippingZoneRepository,
    ) {}

    public function code(): string
    {
        return 'flat_rate';
    }

    public function name(): string
    {
        return 'Flat Rate';
    }

    /**
     * @return array<ShippingRateOption>
     */
    public function calculateRates(ShippingRateRequest $shippingRateRequest): array
    {
        $countryId = (int) ($shippingRateRequest->shippingAddress['country_id'] ?? 0);
        $regionId = isset($shippingRateRequest->shippingAddress['region_id'])
            ? (int) $shippingRateRequest->shippingAddress['region_id']
            : null;

        $shippingZone = $this->shippingZoneRepository->findByCountryAndRegion($countryId, $regionId);

        if ($shippingZone === null) {
            return [];
        }

        $activeMethods = $this->shippingMethodRepository->findActive();
        $rateOptions = [];

        foreach ($activeMethods as $shippingMethod) {
            if ($shippingMethod->carrier_code !== $this->code()) {
                continue;
            }

            if ($shippingMethod->min_order_amount !== null && bccomp($shippingRateRequest->subtotal, $shippingMethod->min_order_amount, 4) < 0) {
                continue;
            }

            if ($shippingMethod->max_order_amount !== null && bccomp($shippingRateRequest->subtotal, $shippingMethod->max_order_amount, 4) > 0) {
                continue;
            }

            $rates = $this->shippingRateRepository->findByMethodAndZone($shippingMethod->id, $shippingZone->id);

            foreach ($rates as $shippingRate) {
                if (! $this->matchesWeightAndSubtotal($shippingRate, $shippingRateRequest)) {
                    continue;
                }

                $price = $shippingRate->price;

                if ($shippingMethod->free_shipping_threshold !== null
                    && bccomp($shippingRateRequest->subtotal, $shippingMethod->free_shipping_threshold, 4) >= 0) {
                    $price = '0.0000';
                }

                $rateOptions[] = new ShippingRateOption(
                    carrierCode: $this->code(),
                    methodCode: $shippingMethod->code,
                    label: $shippingMethod->name,
                    price: $price,
                    estimatedDays: $shippingMethod->config['estimated_days'] ?? null,
                );
            }
        }

        return $rateOptions;
    }

    public function isAvailable(ShippingRateRequest $shippingRateRequest): bool
    {
        return count($this->calculateRates($shippingRateRequest)) > 0;
    }

    private function matchesWeightAndSubtotal(
        ShippingRate $shippingRate,
        ShippingRateRequest $shippingRateRequest,
    ): bool {
        if ($shippingRate->min_weight !== null && $shippingRateRequest->totalWeight !== null) {
            if (bccomp($shippingRateRequest->totalWeight, $shippingRate->min_weight, 4) < 0) {
                return false;
            }
        }

        if ($shippingRate->max_weight !== null && $shippingRateRequest->totalWeight !== null) {
            if (bccomp($shippingRateRequest->totalWeight, $shippingRate->max_weight, 4) > 0) {
                return false;
            }
        }

        if ($shippingRate->min_subtotal !== null) {
            if (bccomp($shippingRateRequest->subtotal, $shippingRate->min_subtotal, 4) < 0) {
                return false;
            }
        }

        if ($shippingRate->max_subtotal !== null) {
            if (bccomp($shippingRateRequest->subtotal, $shippingRate->max_subtotal, 4) > 0) {
                return false;
            }
        }

        return true;
    }
}
