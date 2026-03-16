<?php

declare(strict_types=1);

namespace App\Catalog\Facades;

use App\Catalog\Contracts\CategoryFacade as CategoryFacadeContract;
use App\Catalog\Models\Category;
use App\Catalog\Repositories\CategoryRepository;
use Illuminate\Support\Collection;

class CategoryFacade implements CategoryFacadeContract
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {}

    public function getCategory(string $uuid): ?Category
    {
        return $this->categoryRepository->findByUuid($uuid);
    }

    public function getCategoryTree(): Collection
    {
        $categories = $this->categoryRepository->getTree();

        return $this->buildNestedTree($categories);
    }

    public function getRootCategories(): Collection
    {
        return $this->categoryRepository->getRootCategories();
    }

    public function getCategoryWithChildren(string $uuid): ?Category
    {
        $category = $this->categoryRepository->findByUuid($uuid);

        if ($category === null) {
            return null;
        }

        $category->load('children');

        return $category;
    }

    private function buildNestedTree(Collection $categories, ?int $parentId = null): Collection
    {
        return $categories
            ->where('parent_id', $parentId)
            ->values()
            ->map(function (Category $category) use ($categories): Category {
                $children = $this->buildNestedTree($categories, $category->id);
                $category->setRelation('children', $children);

                return $category;
            });
    }
}
