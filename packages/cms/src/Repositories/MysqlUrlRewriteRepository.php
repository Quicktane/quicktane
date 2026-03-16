<?php

declare(strict_types=1);

namespace Quicktane\CMS\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\CMS\Enums\EntityType;
use Quicktane\CMS\Models\UrlRewrite;

class MysqlUrlRewriteRepository implements UrlRewriteRepository
{
    public function __construct(
        private readonly UrlRewrite $urlRewriteModel,
    ) {}

    public function findById(int $id): ?UrlRewrite
    {
        return $this->urlRewriteModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?UrlRewrite
    {
        return $this->urlRewriteModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function resolveByRequestPath(string $requestPath, int $storeViewId = 0): ?UrlRewrite
    {
        return $this->urlRewriteModel->newQuery()
            ->where('request_path', $requestPath)
            ->where(function ($query) use ($storeViewId): void {
                $query->where('store_view_id', $storeViewId)
                    ->orWhere('store_view_id', 0);
            })
            ->orderByRaw('store_view_id = ? DESC', [$storeViewId])
            ->first();
    }

    public function findByEntity(EntityType $entityType, int $entityId): Collection
    {
        return $this->urlRewriteModel->newQuery()
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->urlRewriteModel->newQuery();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters): void {
                $q->where('request_path', 'like', "%{$filters['search']}%")
                    ->orWhere('target_path', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }

        if (isset($filters['store_view_id'])) {
            $query->where('store_view_id', $filters['store_view_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): UrlRewrite
    {
        return $this->urlRewriteModel->newQuery()->create($data);
    }

    public function update(UrlRewrite $urlRewrite, array $data): UrlRewrite
    {
        $urlRewrite->update($data);

        return $urlRewrite;
    }

    public function delete(UrlRewrite $urlRewrite): void
    {
        $urlRewrite->delete();
    }

    public function deleteByEntity(EntityType $entityType, int $entityId): void
    {
        $this->urlRewriteModel->newQuery()
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->delete();
    }
}
