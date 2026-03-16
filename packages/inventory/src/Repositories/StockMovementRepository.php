<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Inventory\Models\StockMovement;

interface StockMovementRepository
{
    public function getByProductAndSource(int $productId, int $sourceId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): StockMovement;
}
