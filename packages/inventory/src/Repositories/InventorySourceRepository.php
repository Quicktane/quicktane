<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Repositories;

use Illuminate\Support\Collection;
use Quicktane\Inventory\Models\InventorySource;

interface InventorySourceRepository
{
    public function findById(int $id): ?InventorySource;

    public function findByUuid(string $uuid): ?InventorySource;

    public function findByCode(string $code): ?InventorySource;

    public function all(): Collection;

    public function getActive(): Collection;

    public function create(array $data): InventorySource;

    public function update(InventorySource $inventorySource, array $data): InventorySource;

    public function delete(InventorySource $inventorySource): bool;
}
