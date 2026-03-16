<?php

declare(strict_types=1);

namespace App\Catalog\Facades;

use App\Catalog\Contracts\ProductFacade as ProductFacadeContract;
use App\Catalog\Models\Product;
use App\Catalog\Repositories\ProductRepository;
use App\Catalog\Services\PricingService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductFacade implements ProductFacadeContract
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly PricingService $pricingService,
    ) {}

    public function getProduct(string $uuid): ?Product
    {
        return $this->productRepository->findByUuid($uuid);
    }

    public function getProductWithDetails(string $uuid): ?Product
    {
        $product = $this->productRepository->findByUuid($uuid);

        if ($product === null) {
            return null;
        }

        $product->load('attributeValues.attribute', 'categories', 'media');

        return $product;
    }

    public function getProductPrice(Product $product): string
    {
        return $this->pricingService->resolvePrice($product);
    }

    public function listProducts(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage, $filters);
    }
}
