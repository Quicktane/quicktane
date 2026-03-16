<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\Category;
use Illuminate\Support\Collection;

class MysqlCategoryRepository implements CategoryRepository
{
    public function __construct(
        private readonly Category $categoryModel,
    ) {}

    public function findById(int $id): ?Category
    {
        return $this->categoryModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Category
    {
        return $this->categoryModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->categoryModel->newQuery()->where('slug', $slug)->first();
    }

    public function getRootCategories(): Collection
    {
        return $this->categoryModel->newQuery()
            ->whereNull('parent_id')
            ->orderBy('position')
            ->get();
    }

    public function getChildren(int $parentId): Collection
    {
        return $this->categoryModel->newQuery()
            ->where('parent_id', $parentId)
            ->orderBy('position')
            ->get();
    }

    public function getTree(): Collection
    {
        return $this->categoryModel->newQuery()
            ->orderBy('level')
            ->orderBy('position')
            ->get();
    }

    public function getAncestors(Category $category): Collection
    {
        $ancestorIds = array_filter(
            explode('/', $category->path),
            fn (string $id): bool => (int) $id !== $category->id,
        );

        if (empty($ancestorIds)) {
            return new Collection;
        }

        return $this->categoryModel->newQuery()
            ->whereIn('id', $ancestorIds)
            ->orderBy('level')
            ->get();
    }

    public function create(array $data): Category
    {
        return $this->categoryModel->newQuery()->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}
