<?php

declare(strict_types=1);

namespace App\Catalog\Services;

use App\Catalog\Events\AfterProductCreate;
use App\Catalog\Events\AfterProductDelete;
use App\Catalog\Events\AfterProductUpdate;
use App\Catalog\Events\BeforeProductCreate;
use App\Catalog\Events\BeforeProductDelete;
use App\Catalog\Events\BeforeProductUpdate;
use App\Catalog\Models\Product;
use App\Catalog\Repositories\AttributeValueRepository;
use App\Catalog\Repositories\ProductRepository;
use Quicktane\Core\Events\EventDispatcher;
use Quicktane\Core\Events\OperationContext;
use Quicktane\Core\Trace\OperationTracer;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly AttributeValueRepository $attributeValueRepository,
        private readonly OperationTracer $operationTracer,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    public function createProduct(
        array $data,
        array $attributeValues = [],
        array $categoryIds = [],
        array $mediaData = [],
    ): Product {
        return $this->operationTracer->execute('product.create', function () use ($data, $attributeValues, $categoryIds, $mediaData): Product {
            $context = new OperationContext;
            $context->set('data', $data);
            $context->set('attribute_values', $attributeValues);
            $context->set('category_ids', $categoryIds);
            $context->set('media_data', $mediaData);

            $this->eventDispatcher->dispatch(new BeforeProductCreate($context));

            $product = $this->productRepository->create($data);

            if (! empty($attributeValues)) {
                $this->syncAttributeValues($product, $attributeValues);
            }

            if (! empty($categoryIds)) {
                $this->syncCategories($product, $categoryIds);
            }

            if (! empty($mediaData)) {
                $this->syncMedia($product, $mediaData);
            }

            $this->eventDispatcher->dispatch(new AfterProductCreate($product, $context));

            return $product;
        });
    }

    public function updateProduct(
        Product $product,
        array $data,
        ?array $attributeValues = null,
        ?array $categoryIds = null,
        ?array $mediaData = null,
    ): Product {
        return $this->operationTracer->execute('product.update', function () use ($product, $data, $attributeValues, $categoryIds, $mediaData): Product {
            $context = new OperationContext;
            $context->set('data', $data);
            $context->set('product_id', $product->id);

            $this->eventDispatcher->dispatch(new BeforeProductUpdate($product, $context));

            $product = $this->productRepository->update($product, $data);

            if ($attributeValues !== null) {
                $this->syncAttributeValues($product, $attributeValues);
            }

            if ($categoryIds !== null) {
                $this->syncCategories($product, $categoryIds);
            }

            if ($mediaData !== null) {
                $this->syncMedia($product, $mediaData);
            }

            $this->eventDispatcher->dispatch(new AfterProductUpdate($product, $context));

            return $product;
        });
    }

    public function deleteProduct(Product $product): bool
    {
        return $this->operationTracer->execute('product.delete', function () use ($product): bool {
            $context = new OperationContext;
            $context->set('product_id', $product->id);
            $context->set('product_sku', $product->sku);

            $this->eventDispatcher->dispatch(new BeforeProductDelete($product, $context));

            $result = $this->productRepository->delete($product);

            $this->eventDispatcher->dispatch(new AfterProductDelete($product, $context));

            return $result;
        });
    }

    public function syncAttributeValues(Product $product, array $attributeValues): void
    {
        $this->attributeValueRepository->deleteByProduct($product->id);

        foreach ($attributeValues as $attributeValue) {
            $this->attributeValueRepository->upsert(
                $product->id,
                (int) $attributeValue['attribute_id'],
                $attributeValue['value'] ?? null,
            );
        }
    }

    public function syncCategories(Product $product, array $categoryIds): void
    {
        $syncData = [];

        foreach ($categoryIds as $index => $categoryId) {
            $syncData[$categoryId] = ['position' => $index];
        }

        $product->categories()->sync($syncData);
    }

    public function syncMedia(Product $product, array $mediaData): void
    {
        $syncData = [];

        foreach ($mediaData as $media) {
            $syncData[$media['media_file_id']] = [
                'position' => $media['position'] ?? 0,
                'label' => $media['label'] ?? null,
                'is_main' => $media['is_main'] ?? false,
            ];
        }

        $product->media()->sync($syncData);
    }
}
