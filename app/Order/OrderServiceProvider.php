<?php

declare(strict_types=1);

namespace App\Order;

use App\Order\Contracts\CreditMemoFacade as CreditMemoFacadeContract;
use App\Order\Contracts\InvoiceFacade as InvoiceFacadeContract;
use App\Order\Contracts\OrderFacade as OrderFacadeContract;
use App\Order\Contracts\OrderStatusFacade as OrderStatusFacadeContract;
use App\Order\Events\AfterOrderStatusChange;
use App\Order\Facades\CreditMemoFacade;
use App\Order\Facades\InvoiceFacade;
use App\Order\Facades\OrderFacade;
use App\Order\Facades\OrderStatusFacade;
use App\Order\Listeners\ReleaseInventoryOnCancel;
use App\Order\Repositories\CreditMemoRepository;
use App\Order\Repositories\InvoiceRepository;
use App\Order\Repositories\MysqlCreditMemoRepository;
use App\Order\Repositories\MysqlInvoiceRepository;
use App\Order\Repositories\MysqlOrderHistoryRepository;
use App\Order\Repositories\MysqlOrderItemRepository;
use App\Order\Repositories\MysqlOrderRepository;
use App\Order\Repositories\OrderHistoryRepository;
use App\Order\Repositories\OrderItemRepository;
use App\Order\Repositories\OrderRepository;
use App\Order\Steps\CreateOrderStep;
use Illuminate\Support\Facades\Event;
use Quicktane\Core\Module\LocalModuleServiceProvider;
use Quicktane\Core\Pipeline\PipelineRegistry;

class OrderServiceProvider extends LocalModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'order';
    }

    public function register(): void
    {
        $this->app->bind(OrderRepository::class, MysqlOrderRepository::class);
        $this->app->bind(OrderItemRepository::class, MysqlOrderItemRepository::class);
        $this->app->bind(OrderHistoryRepository::class, MysqlOrderHistoryRepository::class);
        $this->app->bind(InvoiceRepository::class, MysqlInvoiceRepository::class);
        $this->app->bind(CreditMemoRepository::class, MysqlCreditMemoRepository::class);

        $this->app->bind(OrderFacadeContract::class, OrderFacade::class);
        $this->app->bind(OrderStatusFacadeContract::class, OrderStatusFacade::class);
        $this->app->bind(InvoiceFacadeContract::class, InvoiceFacade::class);
        $this->app->bind(CreditMemoFacadeContract::class, CreditMemoFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();

        /** @var PipelineRegistry $pipelineRegistry */
        $pipelineRegistry = $this->app->make(PipelineRegistry::class);
        $pipelineRegistry->register('checkout.place', CreateOrderStep::class);

        Event::listen(AfterOrderStatusChange::class, ReleaseInventoryOnCancel::class);
    }
}
