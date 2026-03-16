<?php

declare(strict_types=1);

namespace Quicktane\CMS\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\CMS\Models\Page;

class MysqlPageRepository implements PageRepository
{
    public function __construct(
        private readonly Page $pageModel,
    ) {}

    public function findById(int $id): ?Page
    {
        return $this->pageModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Page
    {
        return $this->pageModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByIdentifier(string $identifier): ?Page
    {
        return $this->pageModel->newQuery()->where('identifier', $identifier)->first();
    }

    public function findActiveForStoreView(int $storeViewId): Collection
    {
        return $this->pageModel->newQuery()
            ->where('is_active', true)
            ->where(function ($query) use ($storeViewId): void {
                $query->whereHas('storeViews', function ($q) use ($storeViewId): void {
                    $q->where('store_view_id', $storeViewId)
                        ->orWhere('store_view_id', 0);
                })->orWhereDoesntHave('storeViews');
            })
            ->orderBy('sort_order')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->pageModel->newQuery()->with('storeViews');

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters): void {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('identifier', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Page
    {
        return $this->pageModel->newQuery()->create($data);
    }

    public function update(Page $page, array $data): Page
    {
        $page->update($data);

        return $page;
    }

    public function delete(Page $page): bool
    {
        return (bool) $page->delete();
    }
}
