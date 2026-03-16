<?php

declare(strict_types=1);

namespace Quicktane\Inventory;

use Quicktane\Core\Module\ModuleServiceProvider;
use Quicktane\Inventory\Contracts\ReservationFacade as ReservationFacadeContract;
use Quicktane\Inventory\Contracts\StockFacade as StockFacadeContract;
use Quicktane\Inventory\Facades\ReservationFacade;
use Quicktane\Inventory\Facades\StockFacade;
use Quicktane\Inventory\Repositories\InventorySourceRepository;
use Quicktane\Inventory\Repositories\MysqlInventorySourceRepository;
use Quicktane\Inventory\Repositories\MysqlStockItemRepository;
use Quicktane\Inventory\Repositories\MysqlStockMovementRepository;
use Quicktane\Inventory\Repositories\StockItemRepository;
use Quicktane\Inventory\Repositories\StockMovementRepository;

class InventoryServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'inventory';
    }

    public function register(): void
    {
        $this->app->bind(InventorySourceRepository::class, MysqlInventorySourceRepository::class);
        $this->app->bind(StockItemRepository::class, MysqlStockItemRepository::class);
        $this->app->bind(StockMovementRepository::class, MysqlStockMovementRepository::class);

        $this->app->bind(StockFacadeContract::class, StockFacade::class);
        $this->app->bind(ReservationFacadeContract::class, ReservationFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();
    }
}
