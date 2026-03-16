<?php

declare(strict_types=1);

namespace App\Store\Repositories;

use App\Store\Models\Store;
use Illuminate\Support\Collection;

interface StoreRepository
{
    public function findById(int $id): ?Store;

    public function findByUuid(string $uuid): ?Store;

    public function findByCode(string $code): ?Store;

    public function all(): Collection;

    public function getByWebsite(int $websiteId): Collection;

    public function create(array $data): Store;

    public function update(Store $store, array $data): Store;

    public function delete(Store $store): bool;
}
