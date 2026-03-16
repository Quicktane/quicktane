<?php

declare(strict_types=1);

namespace App\Catalog\Http\Controllers\Storefront;

use App\Catalog\Contracts\CategoryFacade;
use App\Catalog\Http\Resources\CategoryResource;
use App\Catalog\Repositories\CategoryRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $categories = app(CategoryFacade::class)->getCategoryTree();

        $activeCategories = $this->filterActiveCategories($categories);

        return CategoryResource::collection($activeCategories);
    }

    public function show(string $slug): CategoryResource
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if ($category === null || ! $category->is_active) {
            abort(404);
        }

        $category->load(['products' => function ($query): void {
            $query->where('is_active', true);
        }]);

        return new CategoryResource($category);
    }

    private function filterActiveCategories(Collection $categories): Collection
    {
        return $categories->filter(function ($category): bool {
            return $category->is_active;
        })->values();
    }
}
