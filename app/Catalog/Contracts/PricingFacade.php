<?php

declare(strict_types=1);

namespace App\Catalog\Contracts;

use App\Catalog\Models\Product;

interface PricingFacade
{
    public function resolvePrice(Product $product): string;

    public function isOnSale(Product $product): bool;
}
