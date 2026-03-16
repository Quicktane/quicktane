<?php

declare(strict_types=1);

namespace App\Catalog\Facades;

use App\Catalog\Contracts\PricingFacade as PricingFacadeContract;
use App\Catalog\Models\Product;
use App\Catalog\Services\PricingService;

class PricingFacade implements PricingFacadeContract
{
    public function __construct(
        private readonly PricingService $pricingService,
    ) {}

    public function resolvePrice(Product $product): string
    {
        return $this->pricingService->resolvePrice($product);
    }

    public function isOnSale(Product $product): bool
    {
        return $this->pricingService->isOnSale($product);
    }
}
