<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Quicktane\Core\Product\Dto\ProductDto;
use Quicktane\Core\Product\Enums\ProductType;
use Quicktane\Core\Product\Models\Price;
use Quicktane\Core\Product\Services\AttributeGroupService;
use Quicktane\Core\Product\Services\AttributesService;
use Quicktane\Core\Product\Services\ProductService;

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
    public function handle(
        ProductService $productService,
        AttributesService $attributesService,
        AttributeGroupService $attributeGroupService
    ) {
//        $attribute = $attributesService->create(AttributeDto::fromArray([
//            'name' => 'Name',
//            'slug' => 'name',
//            'type' => AttributeType::STRING,
//        ]));
//
//        $attribute1 = $attributesService->create(AttributeDto::fromArray([
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

//                Currency::create([
//            'code' => 'EUR',
//            'name' => 'EUR',
//            'exchange_rate' => 1,
//            'decimal_places' => 2,
//            'default' => false,
//            'enabled' => true,
//        ]);

        $product = $productService->create(ProductDto::fromArray([
            'attribute_group' => 1,
            'type'            => ProductType::SIMPLE,
            'sku'             => 'qwe',
            'quantity'        => 3,
            'attributes'      => [
                'name' => 'My new product',
                'description' => 'My new product description',
                'width' => 1,
                'height' => 1,
                'length' => 1,
            ],
        ]));

        $price = Price::query()->newModelInstance();
        $price->product()->associate($product);
        $price->amount = money(100, 'EUR');
        $price->save();

        $prices = $product->prices;

        dd($prices);

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
