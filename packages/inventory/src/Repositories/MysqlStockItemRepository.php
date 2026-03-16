<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Quicktane\Inventory\Models\StockItem;

class MysqlStockItemRepository implements StockItemRepository
{
    public function __construct(
        private readonly StockItem $stockItemModel,
    ) {}

    public function findByProductAndSource(int $productId, int $sourceId): ?StockItem
    {
        return $this->stockItemModel->newQuery()
            ->where('product_id', $productId)
            ->where('source_id', $sourceId)
            ->first();
    }

    public function getByProduct(int $productId): Collection
    {
        return $this->stockItemModel->newQuery()
            ->where('product_id', $productId)
            ->get();
    }

    public function getBySource(int $sourceId): Collection
    {
        return $this->stockItemModel->newQuery()
            ->where('source_id', $sourceId)
            ->get();
    }

    public function getLowStock(int $perPage = 15): LengthAwarePaginator
    {
        return $this->stockItemModel->newQuery()
            ->whereColumn('quantity', '<=', 'notify_quantity')
            ->where('is_in_stock', true)
            ->paginate($perPage);
    }

    public function upsert(int $productId, int $sourceId, array $data): StockItem
    {
        return $this->stockItemModel->newQuery()->updateOrCreate(
            ['product_id' => $productId, 'source_id' => $sourceId],
            $data,
        );
    }

    public function adjustQuantityAtomic(int $productId, int $sourceId, int $delta): bool
    {
        if ($delta < 0) {
            $affected = $this->stockItemModel->newQuery()
                ->where('product_id', $productId)
                ->where('source_id', $sourceId)
                ->where('quantity', '>=', abs($delta))
                ->update(['quantity' => DB::raw("quantity + ({$delta})")]);

            return $affected > 0;
        }

        $affected = $this->stockItemModel->newQuery()
            ->where('product_id', $productId)
            ->where('source_id', $sourceId)
            ->update(['quantity' => DB::raw("quantity + {$delta}")]);

        return $affected > 0;
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->stockItemModel->newQuery();

        if (isset($filters['source_id'])) {
            $query->where('source_id', $filters['source_id']);
        }

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['is_in_stock'])) {
            $query->where('is_in_stock', $filters['is_in_stock']);
        }

        return $query->paginate($perPage);
    }
}
