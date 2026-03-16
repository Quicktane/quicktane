<?php

declare(strict_types=1);

namespace App\Store\Repositories;

use App\Store\Models\StoreView;
use Illuminate\Support\Collection;

interface StoreViewRepository
{
    public function findById(int $id): ?StoreView;

    public function findByUuid(string $uuid): ?StoreView;

    public function findByCode(string $code): ?StoreView;

    public function all(): Collection;

    public function getByStore(int $storeId): Collection;

    public function create(array $data): StoreView;

    public function update(StoreView $storeView, array $data): StoreView;

    public function delete(StoreView $storeView): bool;
}
