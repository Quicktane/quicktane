<?php

declare(strict_types=1);

namespace Quicktane\CMS\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\CMS\Models\Block;

class MysqlBlockRepository implements BlockRepository
{
    public function __construct(
        private readonly Block $blockModel,
    ) {}

    public function findById(int $id): ?Block
    {
        return $this->blockModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Block
    {
        return $this->blockModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByIdentifier(string $identifier): ?Block
    {
        return $this->blockModel->newQuery()->where('identifier', $identifier)->first();
    }

    public function findActiveForStoreView(int $storeViewId): Collection
    {
        return $this->blockModel->newQuery()
            ->where('is_active', true)
            ->where(function ($query) use ($storeViewId): void {
                $query->whereHas('storeViews', function ($q) use ($storeViewId): void {
                    $q->where('store_view_id', $storeViewId)
                        ->orWhere('store_view_id', 0);
                })->orWhereDoesntHave('storeViews');
            })
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->blockModel->newQuery()->with('storeViews');

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

    public function create(array $data): Block
    {
        return $this->blockModel->newQuery()->create($data);
    }

    public function update(Block $block, array $data): Block
    {
        $block->update($data);

        return $block;
    }

    public function delete(Block $block): bool
    {
        return (bool) $block->delete();
    }
}
