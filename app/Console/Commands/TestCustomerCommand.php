<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Quicktane\Core\Customer\Services\CustomerGroupService;
use Quicktane\Core\Customer\Services\CustomerService;

class TestCustomerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(CustomerService $customerService, CustomerGroupService $customerGroupService)
    {
//        $customerGroupService->create(CustomerGroupDto::fromArray([
//            'name' => 'My new Customer Group'
//        ]));

//        $customerGroupService->update(CustomerGroup::query()->find(1), CustomerGroupDto::fromArray([
//            'name' => 'My new Customer Group new'
//        ]));

//        $customerService->create(CustomerDto::fromArray([
//            'first_name' => 'John',
//            'last_name' => 'Doe',
////            'email' => 'john@doe.com',
//            'phone' => '0123456789',
//        ]));

//        $customerGroupService->attachCustomerToGroup(CustomerGroup::query()->find(1), Customer::query()->find(1));
//        $customerGroupService->detachCustomerToGroup(CustomerGroup::query()->find(1), Customer::query()->find(1));
    }
}
