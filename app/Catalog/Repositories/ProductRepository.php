<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepository
{
    public function findById(int $id): ?Product;

    public function findByUuid(string $uuid): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function findBySku(string $sku): ?Product;

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator;

    public function paginateActive(int $perPage = 20): LengthAwarePaginator;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): bool;
}
