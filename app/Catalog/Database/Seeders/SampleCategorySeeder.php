<?php

declare(strict_types=1);

namespace App\Catalog\Database\Seeders;

use App\Catalog\Models\Category;
use Illuminate\Database\Seeder;

class SampleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $rootCatalog = Category::where('slug', 'root-catalog')->first();

        if ($rootCatalog === null) {
            return;
        }

        $categories = [
            'men' => [
                'name' => 'Men',
                'children' => [
                    ['name' => 'T-Shirts', 'slug' => 'men-t-shirts'],
                    ['name' => 'Jeans', 'slug' => 'men-jeans'],
                    ['name' => 'Jackets', 'slug' => 'men-jackets'],
                    ['name' => 'Shoes', 'slug' => 'men-shoes'],
                ],
            ],
            'women' => [
                'name' => 'Women',
                'children' => [
                    ['name' => 'Dresses', 'slug' => 'women-dresses'],
                    ['name' => 'Tops', 'slug' => 'women-tops'],
                    ['name' => 'Skirts', 'slug' => 'women-skirts'],
                    ['name' => 'Shoes', 'slug' => 'women-shoes'],
                ],
            ],
            'accessories' => [
                'name' => 'Accessories',
                'children' => [
                    ['name' => 'Bags', 'slug' => 'accessories-bags'],
                    ['name' => 'Watches', 'slug' => 'accessories-watches'],
                    ['name' => 'Jewelry', 'slug' => 'accessories-jewelry'],
                    ['name' => 'Sunglasses', 'slug' => 'accessories-sunglasses'],
                ],
            ],
            'electronics' => [
                'name' => 'Electronics',
                'children' => [
                    ['name' => 'Smartphones', 'slug' => 'electronics-smartphones'],
                    ['name' => 'Laptops', 'slug' => 'electronics-laptops'],
                    ['name' => 'Headphones', 'slug' => 'electronics-headphones'],
                ],
            ],
        ];

        $position = 1;

        foreach ($categories as $slug => $categoryData) {
            $parent = Category::firstOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $rootCatalog->id,
                    'name' => $categoryData['name'],
                    'path' => '0',
                    'level' => 1,
                    'position' => $position++,
                    'is_active' => true,
                    'include_in_menu' => true,
                ],
            );

            $parent->update([
                'path' => $rootCatalog->id.'/'.$parent->id,
            ]);

            $childPosition = 0;

            foreach ($categoryData['children'] as $childData) {
                $child = Category::firstOrCreate(
                    ['slug' => $childData['slug']],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childData['name'],
                        'path' => '0',
                        'level' => 2,
                        'position' => $childPosition++,
                        'is_active' => true,
                        'include_in_menu' => true,
                    ],
                );

                $child->update([
                    'path' => $rootCatalog->id.'/'.$parent->id.'/'.$child->id,
                ]);
            }
        }
    }
}
