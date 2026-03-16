<?php

declare(strict_types=1);

namespace App\Catalog\Database\Seeders;

use App\Catalog\Models\AttributeSet;
use Illuminate\Database\Seeder;

class DefaultAttributeSetSeeder extends Seeder
{
    public function run(): void
    {
        AttributeSet::firstOrCreate(
            ['name' => 'Default'],
            [
                'sort_order' => 0,
            ],
        );
    }
}
