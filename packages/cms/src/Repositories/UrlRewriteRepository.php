<?php

declare(strict_types=1);

namespace Quicktane\CMS\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\CMS\Enums\EntityType;
use Quicktane\CMS\Models\UrlRewrite;

interface UrlRewriteRepository
{
    public function findById(int $id): ?UrlRewrite;

    public function findByUuid(string $uuid): ?UrlRewrite;

    public function resolveByRequestPath(string $requestPath, int $storeViewId = 0): ?UrlRewrite;

    public function findByEntity(EntityType $entityType, int $entityId): Collection;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): UrlRewrite;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(UrlRewrite $urlRewrite, array $data): UrlRewrite;

    public function delete(UrlRewrite $urlRewrite): void;

    public function deleteByEntity(EntityType $entityType, int $entityId): void;
}
