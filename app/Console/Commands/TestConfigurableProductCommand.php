<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Quicktane\Core\Product\Dto\ConfigurableProductDto;
use Quicktane\Core\Product\Services\AttributeGroupService;
use Quicktane\Core\Product\Services\AttributeService;
use Quicktane\Core\Product\Services\ProductService;

class TestConfigurableProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:conf-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        ProductService $productService,
        AttributeService $attributesService,
        AttributeGroupService $attributeGroupService
    ) {
//        $attribute = $attributesService->create(CreateAttributeDto::fromArray([
//            'name' => 'Name',
//            'slug' => 'name',
//            'type' => AttributeType::STRING,
//        ]));
//
//        $attribute1 = $attributesService->create(CreateAttributeDto::fromArray([
//            'name' => 'Description',
//            'slug' => 'description',
//            'type' => AttributeType::STRING,
//        ]));
//
//        $attributeGroup = $attributeGroupService->create(AttributeGroupDto::fromArray([
//            'name' => 'Default',
//            'slug' => 'default',
//            'attributes' => [$attribute->id, $attribute1->id]
//        ]));

//        Currency::create([
//            'code' => 'USD',
//            'name' => 'US Dollar',
//            'exchange_rate' => 1,
//            'decimal_places' => 2,
//            'default' => true,
//            'enabled' => true,
//        ]);
//
//                Currency::create([
//            'code' => 'EUR',
//            'name' => 'EUR',
//            'exchange_rate' => 1,
//            'decimal_places' => 2,
//            'default' => false,
//            'enabled' => true,
//        ]);

        $product = $productService->creteConfigurableProduct(ConfigurableProductDto::fromArray([
            'product'              => [
                'attribute_group_id' => 1,
                'sku'                => 'qwe',
                'quantity'           => 3,
                'attributes'         => [
                    'name'        => 'My new product',
                    'description' => 'My new product description',
                    'width'       => 1,
                    'height'      => 1,
                    'length'      => 1,
                ],
            ],
            'configurable_options' => [
                'attribute_options' => [1, 2],
            ],
        ]));
    }
}
