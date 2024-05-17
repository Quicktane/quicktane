<?php

namespace Tests\Feature\Categories;

use Illuminate\Foundation\Testing\WithFaker;
use Quicktane\Core\Category\DTO\CategoriesIdsDto;
use Quicktane\Core\Category\DTO\ProductsIdsDto;
use Quicktane\Core\Category\Models\Category;
use Quicktane\Core\Category\Services\CategorySyncService;
use Quicktane\Core\Product\Models\Product;
use Tests\TestCase;

class CategoriesProduct extends TestCase
{
    use  WithFaker;

    protected CategorySyncService $categoryService;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->categoryService = resolve(CategorySyncService::class);
    }

    public function testAttachCategories()
    {
        $categories = [
            Category::factory()->create()->id,
            Category::factory()->create()->id,
            Category::factory()->create()->id,
        ];

        $product = Product::factory()->create();

        $products = ProductsIdsDto::fromArray(['products' => [$product->id]]);
        $categories = CategoriesIdsDto::fromArray(['categories' => $categories]);

        $this->categoryService->attach($products, $categories);

        $this->assertTrue($product->categories()->count() === 3);
    }
}
