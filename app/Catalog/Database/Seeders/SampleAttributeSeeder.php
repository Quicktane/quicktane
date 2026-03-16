<?php

declare(strict_types=1);

namespace App\Catalog\Database\Seeders;

use App\Catalog\Models\Attribute;
use App\Catalog\Models\AttributeOption;
use App\Catalog\Models\AttributeSet;
use Illuminate\Database\Seeder;

class SampleAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $colorAttribute = Attribute::firstOrCreate(
            ['code' => 'color'],
            [
                'name' => 'Color',
                'type' => 'select',
                'is_required' => false,
                'is_filterable' => true,
                'is_visible' => true,
                'sort_order' => 0,
            ],
        );

        $this->createOptions($colorAttribute, [
            ['label' => 'Red', 'value' => 'red', 'sort_order' => 0],
            ['label' => 'Blue', 'value' => 'blue', 'sort_order' => 1],
            ['label' => 'Green', 'value' => 'green', 'sort_order' => 2],
            ['label' => 'Black', 'value' => 'black', 'sort_order' => 3],
            ['label' => 'White', 'value' => 'white', 'sort_order' => 4],
        ]);

        $sizeAttribute = Attribute::firstOrCreate(
            ['code' => 'size'],
            [
                'name' => 'Size',
                'type' => 'select',
                'is_required' => false,
                'is_filterable' => true,
                'is_visible' => true,
                'sort_order' => 1,
            ],
        );

        $this->createOptions($sizeAttribute, [
            ['label' => 'XS', 'value' => 'xs', 'sort_order' => 0],
            ['label' => 'S', 'value' => 's', 'sort_order' => 1],
            ['label' => 'M', 'value' => 'm', 'sort_order' => 2],
            ['label' => 'L', 'value' => 'l', 'sort_order' => 3],
            ['label' => 'XL', 'value' => 'xl', 'sort_order' => 4],
            ['label' => 'XXL', 'value' => 'xxl', 'sort_order' => 5],
        ]);

        $materialAttribute = Attribute::firstOrCreate(
            ['code' => 'material'],
            [
                'name' => 'Material',
                'type' => 'select',
                'is_required' => false,
                'is_filterable' => true,
                'is_visible' => true,
                'sort_order' => 2,
            ],
        );

        $this->createOptions($materialAttribute, [
            ['label' => 'Cotton', 'value' => 'cotton', 'sort_order' => 0],
            ['label' => 'Polyester', 'value' => 'polyester', 'sort_order' => 1],
            ['label' => 'Wool', 'value' => 'wool', 'sort_order' => 2],
            ['label' => 'Silk', 'value' => 'silk', 'sort_order' => 3],
            ['label' => 'Leather', 'value' => 'leather', 'sort_order' => 4],
        ]);

        $weightAttribute = Attribute::firstOrCreate(
            ['code' => 'weight'],
            [
                'name' => 'Weight',
                'type' => 'decimal',
                'is_required' => false,
                'is_filterable' => false,
                'is_visible' => true,
                'sort_order' => 3,
            ],
        );

        $brandAttribute = Attribute::firstOrCreate(
            ['code' => 'brand'],
            [
                'name' => 'Brand',
                'type' => 'text',
                'is_required' => false,
                'is_filterable' => true,
                'is_visible' => true,
                'sort_order' => 4,
            ],
        );

        $clothingSet = AttributeSet::firstOrCreate(
            ['name' => 'Clothing'],
            [
                'sort_order' => 1,
            ],
        );

        $clothingSet->attributes()->syncWithoutDetaching([
            $colorAttribute->id => ['group_name' => 'General', 'sort_order' => 0],
            $sizeAttribute->id => ['group_name' => 'General', 'sort_order' => 1],
            $materialAttribute->id => ['group_name' => 'General', 'sort_order' => 2],
            $weightAttribute->id => ['group_name' => 'Specifications', 'sort_order' => 3],
            $brandAttribute->id => ['group_name' => 'General', 'sort_order' => 4],
        ]);
    }

    private function createOptions(Attribute $attribute, array $options): void
    {
        foreach ($options as $option) {
            AttributeOption::firstOrCreate(
                [
                    'attribute_id' => $attribute->id,
                    'value' => $option['value'],
                ],
                $option,
            );
        }
    }
}
