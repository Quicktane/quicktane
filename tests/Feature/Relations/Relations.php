<?php

namespace Tests\Feature\Relations;

use App\Models\User;
use Quicktane\Core\Models\Address;
use Quicktane\Core\Models\Country;
use Quicktane\Core\Models\Customer;
use Quicktane\Core\Models\Discount;
use Quicktane\Core\Models\Language;
use Quicktane\Core\Models\Url;
use Tests\TestCase;

class Relations extends TestCase
{
    public function testAddress()
    {
        dd(Customer::query()->first()->users->count());
        $customer = Customer::factory()->create();
        $country =  Country::factory()->create();

        $address = Address::factory()->create([
            'customer_id' => $customer->id,
            'country_id' => $country->id,
        ]);

        /** @var Address $address */
        $this->assertInstanceOf(Customer::class, $address->customer);
        $this->assertEquals($customer->id, $address->customer->id);

        $this->assertInstanceOf(Country::class, $address->country);
        $this->assertEquals($country->id, $address->country->id);
    }

//    public function testDiscount()
//    {
//        /** @var Discount $discount */
//        $discount = Discount::factory()->create();
//        dd($discount->users()->sync(User::factory()->create()));
////        /** @var Address $address */
////        $this->assertInstanceOf(Customer::class, $address->customer);
////        $this->assertEquals($customer->id, $address->customer->id);
////
////        $this->assertInstanceOf(Country::class, $address->country);
////        $this->assertEquals($country->id, $address->country->id);
//    }

    public function testUrl()
    {
        $language = Language::factory()->create();
        $url = Url::factory()->create(['language_id' => $language->id]);

        $this->assertInstanceOf(Language::class, $url->language);
        $this->assertEquals($language->id, $url->language->id);
    }
}
