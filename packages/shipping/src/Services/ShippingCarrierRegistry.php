<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Services;

use Quicktane\Shipping\Contracts\ShippingCarrier;

class ShippingCarrierRegistry
{
    /** @var array<string, ShippingCarrier> */
    private array $carriers = [];

    public function register(ShippingCarrier $shippingCarrier): void
    {
        $this->carriers[$shippingCarrier->code()] = $shippingCarrier;
    }

    public function getCarrier(string $code): ?ShippingCarrier
    {
        return $this->carriers[$code] ?? null;
    }

    /**
     * @return array<ShippingCarrier>
     */
    public function getActiveCarriers(): array
    {
        return array_values($this->carriers);
    }
}
