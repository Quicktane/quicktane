<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Facades;

use Quicktane\Inventory\Contracts\ReservationFacade as ReservationFacadeContract;

class ReservationFacade implements ReservationFacadeContract
{
    public function reserve(int $productId, int $sourceId, int $quantity): bool
    {
        return true;
    }

    public function release(int $productId, int $sourceId, int $quantity): bool
    {
        return true;
    }

    public function getReservedQuantity(int $productId, int $sourceId): int
    {
        return 0;
    }
}
