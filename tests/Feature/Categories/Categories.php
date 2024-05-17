<?php

namespace Tests\Feature\Categories;

use Illuminate\Foundation\Testing\WithFaker;
use Quicktane\Core\Category\DTO\CategoryTree;
use Quicktane\Core\Category\Models\Category;
use Quicktane\Core\Category\Services\CategoryService;
use Tests\TestCase;

class Categories extends TestCase
{
    use  WithFaker;

    protected CategoryService $categoryService;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->categoryService = resolve(CategoryService::class);
    }

    /**
     * @see CategoryService::tree()
     */
    public function testListCategoryTree()
    {
        //TODO compare structure
        $this->assertTrue((bool)$this->categoryService->tree());
    }

    /**
     * @see CategoryService::rebuildTree()
     */
    public function testRebuildTree()
    {
        $categories = [
            [
                'name' => 1,
                'slug' => $this->faker->slug,
                'children' => [
                    'name' => 4,
                    'slug' => $this->faker->slug,
                ]
            ],
            [
                'name' => 2,
                'slug' => $this->faker->slug,
                'children' => [
                    'name' => 3,
                    'slug' => $this->faker->slug,
                ]
            ],
        ];

        $categoryTree = CategoryTree::fromArray($categories);

        $this->categoryService->rebuildTree($categoryTree);

        $this->assertTrue(true);
    }

    /**
     * @see CategoryService::rebuildTree()
     */
    public function testRebuildExistTree()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $categories = [
            [
                'id' => $category1->id,
                'name' => 1,
                'slug' => $this->faker->slug,
            ],
            [
                'id' => $category2->id,
                'name' => 2,
                'slug' => $this->faker->slug,
            ],
        ];

        $categoryTree = CategoryTree::fromArray($categories);

        $this->categoryService->rebuildTree($categoryTree);

        $this->assertTrue($category1->fresh()->name == 1);
        $this->assertTrue($category2->fresh()->name == 2);
    }
}
