<?php

declare(strict_types=1);

namespace App\Catalog\Services;

use App\Catalog\Models\Product;
use Carbon\Carbon;

class PricingService
{
    public function resolvePrice(Product $product): string
    {
        if ($product->special_price !== null && $this->isSpecialPriceActive($product)) {
            return $product->special_price;
        }

        return $product->base_price;
    }

    public function isOnSale(Product $product): bool
    {
        return $product->special_price !== null && $this->isSpecialPriceActive($product);
    }

    private function isSpecialPriceActive(Product $product): bool
    {
        $now = Carbon::now();

        if ($product->special_price_from !== null && $now->lt($product->special_price_from)) {
            return false;
        }

        if ($product->special_price_to !== null && $now->gt($product->special_price_to)) {
            return false;
        }

        return true;
    }
}
