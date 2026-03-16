<?php

declare(strict_types=1);

namespace App\Store\Repositories;

use App\Store\Models\StoreView;
use Illuminate\Support\Collection;

class MysqlStoreViewRepository implements StoreViewRepository
{
    public function __construct(
        private readonly StoreView $storeViewModel,
    ) {}

    public function findById(int $id): ?StoreView
    {
        return $this->storeViewModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?StoreView
    {
        return $this->storeViewModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCode(string $code): ?StoreView
    {
        return $this->storeViewModel->newQuery()->where('code', $code)->first();
    }

    public function all(): Collection
    {
        return $this->storeViewModel->newQuery()->orderBy('sort_order')->get();
    }

    public function getByStore(int $storeId): Collection
    {
        return $this->storeViewModel->newQuery()
            ->where('store_id', $storeId)
            ->orderBy('sort_order')
            ->get();
    }

    public function create(array $data): StoreView
    {
        return $this->storeViewModel->newQuery()->create($data);
    }

    public function update(StoreView $storeView, array $data): StoreView
    {
        $storeView->update($data);

        return $storeView;
    }

    public function delete(StoreView $storeView): bool
    {
        return (bool) $storeView->delete();
    }
}
