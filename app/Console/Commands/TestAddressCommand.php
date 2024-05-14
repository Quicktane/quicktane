<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Quicktane\Core\Customer\Dto\AddressDto;
use Quicktane\Core\Customer\Dto\CustomerDto;
use Quicktane\Core\Customer\Enums\AddressType;
use Quicktane\Core\Customer\Services\AddressService;
use Quicktane\Core\Customer\Services\CustomerService;

class TestAddressCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(CustomerService $customerService, AddressService $addressService)
    {
        $customer = $customerService->create(CustomerDto::fromArray([
            'first_name' => 'John',
            'last_name'  => 'Doe',
//            'email' => 'john@doe.com',
            'phone'      => '0123456789',
        ]));

        $addressService->create(AddressDto::fromArray([
            'customer_id' => $customer->id,
            'country_id'  => 1,
            'type'        => AddressType::BILLING,
            'line_one'    => 'Kopisto 8',
            'city'        => 'London',
            'first_name'  => 'John',
            'last_name'   => 'Doe',
//            'email' => 'john@doe.com',
            'phone'       => '0123456789',
        ]));
    }
}
