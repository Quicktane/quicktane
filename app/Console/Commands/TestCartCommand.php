<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Quicktane\Core\Cart\Services\CartService;
use Quicktane\Core\Customer\Models\Customer;
use Quicktane\Core\Customer\Services\CustomerService;
use Quicktane\Core\Models\Currency;
use Quicktane\Core\Product\Dto\ProductDto;
use Quicktane\Core\Product\Enums\ProductType;
use Quicktane\Core\Product\Services\ProductService;

class TestCartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ProductService $productService, CustomerService $customerService, CartService $cartService)
    {
//        $currency = Currency::create([
//            'code'           => 'USD',
//            'name'           => 'US Dollar',
//            'exchange_rate'  => 1,
//            'decimal_places' => 2,
//            'default'        => true,
//            'enabled'        => true,
//        ]);

        $product = $productService->create(ProductDto::fromArray([
            'attribute_group' => 1,
            'type'            => ProductType::SIMPLE,
            'sku'             => 'qwe',
            'quantity'        => 3,
            'attributes'      => [
                'name'        => 'My new product',
                'description' => 'My new product description',
                'width'       => 1,
                'height'      => 1,
                'length'      => 1,
            ],
        ]));

//        $customer = $customerService->create(CustomerDto::fromArray([
//            'first_name' => 'John',
//            'last_name'  => 'Doe',
////            'email' => 'john@doe.com',
//            'phone'      => '0123456789',
//        ]));

        $cart = $cartService->create(Customer::query()->find(1), Currency::query()->find(1));

        $item = $cartService->addItem($cart, $product);
        $cartService->deleteItem($cart, $item);

        dd($item);
    }
}
