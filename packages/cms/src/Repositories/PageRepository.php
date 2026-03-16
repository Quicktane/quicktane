<?php

declare(strict_types=1);

namespace Quicktane\CMS\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\CMS\Models\Page;

interface PageRepository
{
    public function findById(int $id): ?Page;

    public function findByUuid(string $uuid): ?Page;

    public function findByIdentifier(string $identifier): ?Page;

    public function findActiveForStoreView(int $storeViewId): Collection;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Page;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Page $page, array $data): Page;

    public function delete(Page $page): bool;
}
