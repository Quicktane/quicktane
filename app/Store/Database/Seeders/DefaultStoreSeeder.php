<?php

declare(strict_types=1);

namespace App\Store\Database\Seeders;

use App\Store\Models\Store;
use App\Store\Models\StoreView;
use App\Store\Models\Website;
use Illuminate\Database\Seeder;

class DefaultStoreSeeder extends Seeder
{
    public function run(): void
    {
        $website = Website::firstOrCreate(
            ['code' => 'main'],
            [
                'name' => 'Main Website',
                'sort_order' => 0,
                'is_active' => true,
            ],
        );

        $store = Store::firstOrCreate(
            ['code' => 'main_store'],
            [
                'website_id' => $website->id,
                'name' => 'Main Store',
                'sort_order' => 0,
                'is_active' => true,
            ],
        );

        StoreView::firstOrCreate(
            ['code' => 'default'],
            [
                'store_id' => $store->id,
                'name' => 'Default Store View',
                'locale' => 'en_US',
                'sort_order' => 0,
                'is_active' => true,
            ],
        );
    }
}
