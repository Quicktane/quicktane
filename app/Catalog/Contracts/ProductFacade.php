<?php

declare(strict_types=1);

namespace App\Catalog\Contracts;

use App\Catalog\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductFacade
{
    public function getProduct(string $uuid): ?Product;

    public function getProductWithDetails(string $uuid): ?Product;

    public function getProductPrice(Product $product): string;

    public function listProducts(int $perPage = 20, array $filters = []): LengthAwarePaginator;
}
