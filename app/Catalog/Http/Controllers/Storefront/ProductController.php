<?php

declare(strict_types=1);

namespace App\Catalog\Http\Controllers\Storefront;

use App\Catalog\Http\Resources\ProductResource;
use App\Catalog\Repositories\ProductRepository;
use App\Catalog\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly PricingService $pricingService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 20);
        $products = $this->productRepository->paginateActive($perPage);

        $products->getCollection()->each(function ($product): void {
            $product->resolved_price = $this->pricingService->resolvePrice($product);
            $product->is_on_sale = $this->pricingService->isOnSale($product);
        });

        return ProductResource::collection($products);
    }

    public function show(string $slug): ProductResource
    {
        $product = $this->productRepository->findBySlug($slug);

        if ($product === null || ! $product->is_active) {
            abort(404);
        }

        $product->load('attributeValues.attribute', 'categories', 'media');

        $product->resolved_price = $this->pricingService->resolvePrice($product);
        $product->is_on_sale = $this->pricingService->isOnSale($product);

        return new ProductResource($product);
    }
}
