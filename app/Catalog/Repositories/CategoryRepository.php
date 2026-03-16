<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepository
{
    public function findById(int $id): ?Category;

    public function findByUuid(string $uuid): ?Category;

    public function findBySlug(string $slug): ?Category;

    public function getRootCategories(): Collection;

    public function getChildren(int $parentId): Collection;

    public function getTree(): Collection;

    public function getAncestors(Category $category): Collection;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): bool;
}
