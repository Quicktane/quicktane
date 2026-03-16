<?php

declare(strict_types=1);

namespace App\Catalog\Services;

use App\Catalog\Models\Category;
use App\Catalog\Repositories\CategoryRepository;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {}

    public function createCategory(array $data): Category
    {
        $parentId = $data['parent_id'] ?? null;

        $data['path'] = '0';
        $data['level'] = 0;

        if ($parentId !== null) {
            $parent = $this->categoryRepository->findById((int) $parentId);

            if ($parent !== null) {
                $data['level'] = $parent->level + 1;
            }
        }

        $category = $this->categoryRepository->create($data);

        $path = $this->buildPath($parentId !== null ? (int) $parentId : null, $category->id);
        $this->categoryRepository->update($category, ['path' => $path]);

        return $category;
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $parentChanged = isset($data['parent_id']) && $data['parent_id'] !== $category->parent_id;

        $category = $this->categoryRepository->update($category, $data);

        if ($parentChanged) {
            $newParentId = $data['parent_id'] !== null ? (int) $data['parent_id'] : null;
            $newPath = $this->buildPath($newParentId, $category->id);
            $newLevel = 0;

            if ($newParentId !== null) {
                $parent = $this->categoryRepository->findById($newParentId);

                if ($parent !== null) {
                    $newLevel = $parent->level + 1;
                }
            }

            $oldPath = $category->path;

            $this->categoryRepository->update($category, [
                'path' => $newPath,
                'level' => $newLevel,
            ]);

            $this->updateDescendantPaths($oldPath, $newPath, $newLevel);
        }

        return $category;
    }

    public function deleteCategory(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }

    public function buildPath(?int $parentId, int $categoryId): string
    {
        if ($parentId === null) {
            return (string) $categoryId;
        }

        $parent = $this->categoryRepository->findById($parentId);

        if ($parent === null) {
            return (string) $categoryId;
        }

        return $parent->path.'/'.$categoryId;
    }

    private function updateDescendantPaths(string $oldPath, string $newPath, int $parentLevel): void
    {
        $allCategories = $this->categoryRepository->getTree();

        $descendants = $allCategories->filter(function (Category $category) use ($oldPath): bool {
            return str_starts_with($category->path, $oldPath.'/') && $category->path !== $oldPath;
        });

        foreach ($descendants as $descendant) {
            $updatedPath = $newPath.substr($descendant->path, strlen($oldPath));
            $depthFromParent = substr_count($updatedPath, '/');

            $this->categoryRepository->update($descendant, [
                'path' => $updatedPath,
                'level' => $depthFromParent,
            ]);
        }
    }
}
