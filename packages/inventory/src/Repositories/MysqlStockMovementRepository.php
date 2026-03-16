<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Inventory\Models\StockMovement;

class MysqlStockMovementRepository implements StockMovementRepository
{
    public function __construct(
        private readonly StockMovement $stockMovementModel,
    ) {}

    public function getByProductAndSource(int $productId, int $sourceId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->stockMovementModel->newQuery()
            ->where('product_id', $productId)
            ->where('source_id', $sourceId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function create(array $data): StockMovement
    {
        return $this->stockMovementModel->newQuery()->create($data);
    }
}
