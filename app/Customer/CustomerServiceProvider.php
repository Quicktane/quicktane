<?php

declare(strict_types=1);

namespace App\Customer;

use App\Customer\Contracts\CustomerFacade as CustomerFacadeContract;
use App\Customer\Contracts\CustomerGroupFacade as CustomerGroupFacadeContract;
use App\Customer\Facades\CustomerFacade;
use App\Customer\Facades\CustomerGroupFacade;
use App\Customer\Repositories\CustomerAddressRepository;
use App\Customer\Repositories\CustomerGroupRepository;
use App\Customer\Repositories\CustomerRepository;
use App\Customer\Repositories\CustomerSegmentRepository;
use App\Customer\Repositories\MysqlCustomerAddressRepository;
use App\Customer\Repositories\MysqlCustomerGroupRepository;
use App\Customer\Repositories\MysqlCustomerRepository;
use App\Customer\Repositories\MysqlCustomerSegmentRepository;
use Illuminate\Support\Facades\Event;
use Quicktane\Core\Module\LocalModuleServiceProvider;

class CustomerServiceProvider extends LocalModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'customer';
    }

    public function register(): void
    {
        $this->app->bind(CustomerRepository::class, MysqlCustomerRepository::class);
        $this->app->bind(CustomerAddressRepository::class, MysqlCustomerAddressRepository::class);
        $this->app->bind(CustomerGroupRepository::class, MysqlCustomerGroupRepository::class);
        $this->app->bind(CustomerSegmentRepository::class, MysqlCustomerSegmentRepository::class);

        $this->app->bind(CustomerFacadeContract::class, CustomerFacade::class);
        $this->app->bind(CustomerGroupFacadeContract::class, CustomerGroupFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();

        Event::listen(
            Events\AfterCustomerLogin::class,
            Listeners\MergeGuestCartListener::class,
        );
    }
}
