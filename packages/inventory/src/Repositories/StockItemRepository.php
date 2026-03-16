<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Inventory\Models\StockItem;

interface StockItemRepository
{
    public function findByProductAndSource(int $productId, int $sourceId): ?StockItem;

    public function getByProduct(int $productId): Collection;

    public function getBySource(int $sourceId): Collection;

    public function getLowStock(int $perPage = 15): LengthAwarePaginator;

    public function upsert(int $productId, int $sourceId, array $data): StockItem;

    public function adjustQuantityAtomic(int $productId, int $sourceId, int $delta): bool;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
}
