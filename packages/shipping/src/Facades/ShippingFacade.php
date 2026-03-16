<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Facades;

use Quicktane\Shipping\Contracts\ShippingCarrier;
use Quicktane\Shipping\Contracts\ShippingFacade as ShippingFacadeContract;
use Quicktane\Shipping\DataTransferObjects\ShippingRateOption;
use Quicktane\Shipping\DataTransferObjects\ShippingRateRequest;
use Quicktane\Shipping\Models\ShippingMethod;
use Quicktane\Shipping\Repositories\ShippingMethodRepository;
use Quicktane\Shipping\Services\ShippingRateService;

class ShippingFacade implements ShippingFacadeContract
{
    public function __construct(
        private readonly ShippingRateService $shippingRateService,
        private readonly ShippingMethodRepository $shippingMethodRepository,
    ) {}

    /**
     * @return array<ShippingRateOption>
     */
    public function getAvailableRates(ShippingRateRequest $shippingRateRequest): array
    {
        return $this->shippingRateService->getAvailableRates($shippingRateRequest);
    }

    public function getMethod(string $code): ?ShippingMethod
    {
        return $this->shippingMethodRepository->findByCode($code);
    }

    /**
     * @return array<ShippingCarrier>
     */
    public function getActiveCarriers(): array
    {
        return $this->shippingRateService->getActiveCarriers();
    }

    public function resolveRate(string $carrierCode, string $methodCode, ShippingRateRequest $shippingRateRequest): ?ShippingRateOption
    {
        return $this->shippingRateService->resolveRate($carrierCode, $methodCode, $shippingRateRequest);
    }
}
