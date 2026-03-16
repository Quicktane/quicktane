<?php

declare(strict_types=1);

namespace App\Catalog\Http\Controllers;

use App\Catalog\Http\Requests\StoreProductRequest;
use App\Catalog\Http\Requests\UpdateProductRequest;
use App\Catalog\Http\Resources\ProductResource;
use App\Catalog\Models\Attribute;
use App\Catalog\Models\AttributeSet;
use App\Catalog\Models\Category;
use App\Catalog\Models\Product;
use App\Catalog\Repositories\ProductRepository;
use App\Catalog\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductService $productService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 20);
        $filters = array_filter([
            'type' => $request->query('type'),
            'is_active' => $request->query('is_active'),
            'attribute_set_id' => $request->query('attribute_set_id'),
            'category_id' => $request->query('category_id'),
        ], fn (mixed $value): bool => $value !== null);

        $products = $this->productRepository->paginate($perPage, $filters);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data = $this->resolveUuidsToIds($data);

        $attributeValues = $data['attribute_values'] ?? [];
        $categoryIds = $data['category_ids'] ?? [];
        $mediaData = $data['media'] ?? [];

        unset($data['attribute_values'], $data['category_ids'], $data['media']);

        $product = $this->productService->createProduct($data, $attributeValues, $categoryIds, $mediaData);
        $product->load('attributeSet', 'attributeValues.attribute', 'categories', 'media');

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        $product->load('attributeSet', 'attributeValues.attribute', 'categories', 'media');

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $data = $request->validated();

        $data = $this->resolveUuidsToIds($data);

        $attributeValues = $data['attribute_values'] ?? null;
        $categoryIds = $data['category_ids'] ?? null;
        $mediaData = $data['media'] ?? null;

        unset($data['attribute_values'], $data['category_ids'], $data['media']);

        $product = $this->productService->updateProduct($product, $data, $attributeValues, $categoryIds, $mediaData);
        $product->load('attributeSet', 'attributeValues.attribute', 'categories', 'media');

        return new ProductResource($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return response()->json(null, 204);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function resolveUuidsToIds(array $data): array
    {
        if (isset($data['attribute_set_uuid'])) {
            $attributeSet = AttributeSet::where('uuid', $data['attribute_set_uuid'])->firstOrFail();
            $data['attribute_set_id'] = $attributeSet->id;
            unset($data['attribute_set_uuid']);
        }

        if (isset($data['attribute_values'])) {
            $data['attribute_values'] = array_map(function (array $attributeValue): array {
                if (isset($attributeValue['attribute_uuid'])) {
                    $attribute = Attribute::where('uuid', $attributeValue['attribute_uuid'])->firstOrFail();
                    $attributeValue['attribute_id'] = $attribute->id;
                    unset($attributeValue['attribute_uuid']);
                }

                return $attributeValue;
            }, $data['attribute_values']);
        }

        if (isset($data['category_uuids'])) {
            $data['category_ids'] = Category::whereIn('uuid', $data['category_uuids'])
                ->pluck('id')
                ->toArray();
            unset($data['category_uuids']);
        }

        if (isset($data['media'])) {
            $data['media'] = array_map(function (array $media): array {
                if (isset($media['media_file_uuid'])) {
                    $media['media_file_id'] = DB::table('media_files')
                        ->where('uuid', $media['media_file_uuid'])
                        ->value('id');
                    unset($media['media_file_uuid']);
                }

                return $media;
            }, $data['media']);
        }

        return $data;
    }
}
