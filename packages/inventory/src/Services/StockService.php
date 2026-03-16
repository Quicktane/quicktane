<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Services;

use Illuminate\Support\Collection;
use Quicktane\Inventory\Models\StockItem;
use Quicktane\Inventory\Repositories\StockItemRepository;
use Quicktane\Inventory\Repositories\StockMovementRepository;

class StockService
{
    public function __construct(
        private readonly StockItemRepository $stockItemRepository,
        private readonly StockMovementRepository $stockMovementRepository,
    ) {}

    public function setStock(int $productId, int $sourceId, int $quantity, string $reason, ?int $userId = null): StockItem
    {
        $existingStockItem = $this->stockItemRepository->findByProductAndSource($productId, $sourceId);
        $currentQuantity = $existingStockItem?->quantity ?? 0;
        $currentReserved = $existingStockItem?->reserved ?? 0;
        $delta = $quantity - $currentQuantity;

        $stockItem = $this->stockItemRepository->upsert($productId, $sourceId, [
            'quantity' => $quantity,
            'is_in_stock' => $quantity > $currentReserved,
        ]);

        if ($delta !== 0 && config('inventory.inventory.track_stock_movements', true)) {
            $this->stockMovementRepository->create([
                'product_id' => $productId,
                'source_id' => $sourceId,
                'quantity_change' => $delta,
                'reason' => $reason,
                'user_id' => $userId,
            ]);
        }

        return $stockItem;
    }

    public function adjustStock(int $productId, int $sourceId, int $delta, string $reason, ?int $userId = null): bool
    {
        $success = $this->stockItemRepository->adjustQuantityAtomic($productId, $sourceId, $delta);

        if (! $success) {
            return false;
        }

        if (config('inventory.inventory.track_stock_movements', true)) {
            $this->stockMovementRepository->create([
                'product_id' => $productId,
                'source_id' => $sourceId,
                'quantity_change' => $delta,
                'reason' => $reason,
                'user_id' => $userId,
            ]);
        }

        $stockItem = $this->stockItemRepository->findByProductAndSource($productId, $sourceId);

        if ($stockItem !== null) {
            $isInStock = $stockItem->quantity > $stockItem->reserved;

            if ($stockItem->is_in_stock !== $isInStock) {
                $this->stockItemRepository->upsert($productId, $sourceId, [
                    'is_in_stock' => $isInStock,
                ]);
            }
        }

        return true;
    }

    public function getSalableQuantity(int $productId): int
    {
        $stockItems = $this->stockItemRepository->getByProduct($productId);

        return $stockItems
            ->filter(fn (StockItem $stockItem): bool => $stockItem->is_in_stock)
            ->sum(fn (StockItem $stockItem): int => $stockItem->quantity - $stockItem->reserved);
    }

    public function isInStock(int $productId): bool
    {
        $stockItems = $this->stockItemRepository->getByProduct($productId);

        return $stockItems->contains(
            fn (StockItem $stockItem): bool => $stockItem->is_in_stock && $stockItem->quantity > $stockItem->reserved,
        );
    }

    public function getStockByProduct(int $productId): Collection
    {
        return $this->stockItemRepository->getByProduct($productId);
    }
}
