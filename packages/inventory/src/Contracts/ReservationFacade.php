<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Contracts;

interface ReservationFacade
{
    public function reserve(int $productId, int $sourceId, int $quantity): bool;

    public function release(int $productId, int $sourceId, int $quantity): bool;

    public function getReservedQuantity(int $productId, int $sourceId): int;
}
