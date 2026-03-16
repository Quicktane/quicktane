<?php

declare(strict_types=1);

namespace App\Catalog\Database\Seeders;

use App\Catalog\Models\Category;
use App\Store\Models\Store;
use Illuminate\Database\Seeder;

class DefaultCategorySeeder extends Seeder
{
    public function run(): void
    {
        $rootCatalog = Category::firstOrCreate(
            ['slug' => 'root-catalog'],
            [
                'name' => 'Root Catalog',
                'path' => '0',
                'level' => 0,
                'position' => 0,
                'is_active' => true,
                'include_in_menu' => false,
            ],
        );

        $rootCatalog->update(['path' => (string) $rootCatalog->id]);

        $defaultCategory = Category::firstOrCreate(
            ['slug' => 'default-category'],
            [
                'parent_id' => $rootCatalog->id,
                'name' => 'Default Category',
                'path' => '0',
                'level' => 1,
                'position' => 0,
                'is_active' => true,
                'include_in_menu' => true,
            ],
        );

        $defaultCategory->update([
            'path' => $rootCatalog->id.'/'.$defaultCategory->id,
        ]);

        $mainStore = Store::where('code', 'main_store')->first();

        if ($mainStore !== null) {
            $mainStore->update(['root_category_id' => $rootCatalog->id]);
        }
    }
}
