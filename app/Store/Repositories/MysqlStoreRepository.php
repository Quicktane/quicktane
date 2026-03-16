<?php

declare(strict_types=1);

namespace App\Store\Repositories;

use App\Store\Models\Store;
use Illuminate\Support\Collection;

class MysqlStoreRepository implements StoreRepository
{
    public function __construct(
        private readonly Store $storeModel,
    ) {}

    public function findById(int $id): ?Store
    {
        return $this->storeModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Store
    {
        return $this->storeModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCode(string $code): ?Store
    {
        return $this->storeModel->newQuery()->where('code', $code)->first();
    }

    public function all(): Collection
    {
        return $this->storeModel->newQuery()->orderBy('sort_order')->get();
    }

    public function getByWebsite(int $websiteId): Collection
    {
        return $this->storeModel->newQuery()
            ->where('website_id', $websiteId)
            ->orderBy('sort_order')
            ->get();
    }

    public function create(array $data): Store
    {
        return $this->storeModel->newQuery()->create($data);
    }

    public function update(Store $store, array $data): Store
    {
        $store->update($data);

        return $store;
    }

    public function delete(Store $store): bool
    {
        return (bool) $store->delete();
    }
}
