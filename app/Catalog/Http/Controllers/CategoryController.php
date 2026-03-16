<?php

declare(strict_types=1);

namespace App\Catalog\Http\Controllers;

use App\Catalog\Contracts\CategoryFacade;
use App\Catalog\Http\Requests\MoveCategoryRequest;
use App\Catalog\Http\Requests\StoreCategoryRequest;
use App\Catalog\Http\Requests\UpdateCategoryRequest;
use App\Catalog\Http\Resources\CategoryResource;
use App\Catalog\Models\Category;
use App\Catalog\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $categories = app(CategoryFacade::class)->getCategoryTree();

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Category $category): CategoryResource
    {
        $category->load('children');

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category = $this->categoryService->updateCategory($category, $request->validated());

        return new CategoryResource($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->deleteCategory($category);

        return response()->json(null, 204);
    }

    public function move(MoveCategoryRequest $request, Category $category): CategoryResource
    {
        $data = $request->validated();

        $category = $this->categoryService->updateCategory($category, [
            'parent_id' => $data['parent_id'],
            'position' => $data['position'] ?? $category->position,
        ]);

        return new CategoryResource($category);
    }
}
