<?php

declare(strict_types=1);

namespace App\Store;

use App\Store\Contracts\ConfigurationFacade as ConfigurationFacadeContract;
use App\Store\Contracts\StoreFacade as StoreFacadeContract;
use App\Store\Facades\ConfigurationFacade;
use App\Store\Facades\StoreFacade;
use App\Store\Repositories\ConfigurationRepository;
use App\Store\Repositories\MysqlConfigurationRepository;
use App\Store\Repositories\MysqlStoreRepository;
use App\Store\Repositories\MysqlStoreViewRepository;
use App\Store\Repositories\MysqlWebsiteRepository;
use App\Store\Repositories\StoreRepository;
use App\Store\Repositories\StoreViewRepository;
use App\Store\Repositories\WebsiteRepository;
use Quicktane\Core\Module\LocalModuleServiceProvider;

class StoreServiceProvider extends LocalModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'store';
    }

    public function register(): void
    {
        $this->app->bind(WebsiteRepository::class, MysqlWebsiteRepository::class);
        $this->app->bind(StoreRepository::class, MysqlStoreRepository::class);
        $this->app->bind(StoreViewRepository::class, MysqlStoreViewRepository::class);
        $this->app->bind(ConfigurationRepository::class, MysqlConfigurationRepository::class);

        $this->app->bind(StoreFacadeContract::class, StoreFacade::class);
        $this->app->bind(ConfigurationFacadeContract::class, ConfigurationFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();
    }
}
