<?php

declare(strict_types=1);

namespace Quicktane\CMS\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\CMS\Models\Block;

interface BlockRepository
{
    public function findById(int $id): ?Block;

    public function findByUuid(string $uuid): ?Block;

    public function findByIdentifier(string $identifier): ?Block;

    public function findActiveForStoreView(int $storeViewId): Collection;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Block;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Block $block, array $data): Block;

    public function delete(Block $block): bool;
}
