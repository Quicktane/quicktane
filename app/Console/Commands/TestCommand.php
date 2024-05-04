<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
    public function handle(ProductService $productService)
    {
        $product = $productService->create([
            'attribute_groups' => [1, 2],
            'type'             => 'simple',
            'sku'              => 'qwe',
            'quantity'         => 3,
            'status'           => 'active',
            'attributes'       => [
                'name'        => 'My new product',
                'description' => 'My new product description',
                'width'       => 1,
                'height'      => 1,
                'length'      => 1,
            ],
        ]);

//        $attribute = new Attribute([
//            'name' => 'Name',
//            'slug' => 'name',
//            'type' => 'string',
//        ]);

//        $attribute->save();

//        $product = Product::factory()->create();
//
//        $product->customAttributes()->attach(Attribute::query()->where(['slug' => 'name'])->first());
//
//        $productAttributeValue = new ProductAttributeValue([
//            'value' => 'Some product'
//        ]);
//
//        $productAttributeValue->product()->associate($product);
//        $productAttributeValue->attribute()->associate(1);
//
//        $productAttributeValue->save();

//        /** @var Product $product */
//        $product = Product::find(2);
//
//        $attributesCollection = $product->customAttributeCollection();
//        dd($attributesCollection->attributeKeys());
//
//        $collection = new ProductAttributesCollection();
//
//        dd($collection->getAttributeValue('name'));

//        dd($product->name);
    }
}
