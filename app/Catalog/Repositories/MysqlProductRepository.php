<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MysqlProductRepository implements ProductRepository
{
    public function __construct(
        private readonly Product $productModel,
    ) {}

    public function findById(int $id): ?Product
    {
        return $this->productModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Product
    {
        return $this->productModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->productModel->newQuery()->where('slug', $slug)->first();
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->productModel->newQuery()->where('sku', $sku)->first();
    }

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = $this->productModel->newQuery();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['attribute_set_id'])) {
            $query->where('attribute_set_id', (int) $filters['attribute_set_id']);
        }

        if (isset($filters['category_id'])) {
            $query->whereHas('categories', function ($categoryQuery) use ($filters): void {
                $categoryQuery->where('categories.id', (int) $filters['category_id']);
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function paginateActive(int $perPage = 20): LengthAwarePaginator
    {
        return $this->productModel->newQuery()
            ->where('is_active', true)
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return $this->productModel->newQuery()->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}
