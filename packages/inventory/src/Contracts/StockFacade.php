<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Contracts;

use Illuminate\Support\Collection;
use Quicktane\Inventory\Models\StockItem;

interface StockFacade
{
    public function getSalableQuantity(int $productId): int;

    public function isInStock(int $productId): bool;

    public function getStockByProduct(int $productId): Collection;

    public function setStock(int $productId, int $sourceId, int $quantity, string $reason, ?int $userId = null): StockItem;

    public function adjustStock(int $productId, int $sourceId, int $delta, string $reason, ?int $userId = null): bool;
}
