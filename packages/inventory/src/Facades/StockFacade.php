<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Facades;

use Illuminate\Support\Collection;
use Quicktane\Inventory\Contracts\StockFacade as StockFacadeContract;
use Quicktane\Inventory\Models\StockItem;
use Quicktane\Inventory\Services\StockService;

class StockFacade implements StockFacadeContract
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    public function getSalableQuantity(int $productId): int
    {
        return $this->stockService->getSalableQuantity($productId);
    }

    public function isInStock(int $productId): bool
    {
        return $this->stockService->isInStock($productId);
    }

    public function getStockByProduct(int $productId): Collection
    {
        return $this->stockService->getStockByProduct($productId);
    }

    public function setStock(int $productId, int $sourceId, int $quantity, string $reason, ?int $userId = null): StockItem
    {
        return $this->stockService->setStock($productId, $sourceId, $quantity, $reason, $userId);
    }

    public function adjustStock(int $productId, int $sourceId, int $delta, string $reason, ?int $userId = null): bool
    {
        return $this->stockService->adjustStock($productId, $sourceId, $delta, $reason, $userId);
    }
}
