<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Quicktane\Core\Dto\AttributeDto;
use Quicktane\Core\Dto\AttributeGroupDto;
use Quicktane\Core\Enums\AttributeType;
use Quicktane\Core\Services\AttributeGroupService;
use Quicktane\Core\Services\AttributesService;
use Quicktane\Core\Services\ProductService;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ProductService $productService, AttributesService $attributesService, AttributeGroupService $attributeGroupService)
    {
        $attribute = $attributesService->create(AttributeDto::fromArray([
            'name' => 'Name',
            'slug' => 'name',
            'type' => AttributeType::STRING,
        ]));

        $attribute1 = $attributesService->create(AttributeDto::fromArray([
            'name' => 'Description',
            'slug' => 'description',
            'type' => AttributeType::STRING,
        ]));

        $attributeGroup = $attributeGroupService->create(AttributeGroupDto::fromArray([
            'name' => 'Default',
            'slug' => 'default',
            'attributes' => [$attribute->id, $attribute1->id]
        ]));

//        $product = $productService->create(ProductDto::fromArray([
//            'attribute_group' => 1,
//            'type'            => ProductType::SIMPLE,
//            'sku'             => 'qwe',
//            'quantity'        => 3,
//            'attributes'      => [
//                'name'        => 'My new product',
//                'description' => 'My new product description',
//                'width'       => 1,
//                'height'      => 1,
//                'length'      => 1,
//            ],
//        ]));

//        $product = $productService->update(Product::query()->find(1), ProductDto::fromArray([
//            'attribute_group' => 1,
//            'type'            => ProductType::SIMPLE,
//            'sku'             => 'qweqqq',
//            'quantity'        => 10,
//            'attributes'      => [
//                'name'        => 'My nwqeqweqwe',
//                'description' => 'My new product description',
//                'width'       => 4,
//                'height'      => 2,
//                'length'      => 3,
//            ],
//        ]));
    }
}
