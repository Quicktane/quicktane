<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Repositories;

use Illuminate\Support\Collection;
use Quicktane\Inventory\Models\InventorySource;

class MysqlInventorySourceRepository implements InventorySourceRepository
{
    public function __construct(
        private readonly InventorySource $inventorySourceModel,
    ) {}

    public function findById(int $id): ?InventorySource
    {
        return $this->inventorySourceModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?InventorySource
    {
        return $this->inventorySourceModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCode(string $code): ?InventorySource
    {
        return $this->inventorySourceModel->newQuery()->where('code', $code)->first();
    }

    public function all(): Collection
    {
        return $this->inventorySourceModel->newQuery()->orderBy('sort_order')->get();
    }

    public function getActive(): Collection
    {
        return $this->inventorySourceModel->newQuery()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function create(array $data): InventorySource
    {
        return $this->inventorySourceModel->newQuery()->create($data);
    }

    public function update(InventorySource $inventorySource, array $data): InventorySource
    {
        $inventorySource->update($data);

        return $inventorySource;
    }

    public function delete(InventorySource $inventorySource): bool
    {
        return (bool) $inventorySource->delete();
    }
}
