<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Quicktane\Inventory\Models\InventorySource;

class DefaultInventorySourceSeeder extends Seeder
{
    public function run(): void
    {
        InventorySource::firstOrCreate(
            ['code' => 'default-warehouse'],
            [
                'name' => 'Default Warehouse',
                'is_active' => true,
            ],
        );
    }
}
