<?php

declare(strict_types=1);

namespace App\Directory;

use App\Directory\Contracts\CountryFacade as CountryFacadeContract;
use App\Directory\Contracts\CurrencyFacade as CurrencyFacadeContract;
use App\Directory\Facades\CountryFacade;
use App\Directory\Facades\CurrencyFacade;
use App\Directory\Repositories\CountryRepository;
use App\Directory\Repositories\CurrencyRateRepository;
use App\Directory\Repositories\CurrencyRepository;
use App\Directory\Repositories\MysqlCountryRepository;
use App\Directory\Repositories\MysqlCurrencyRateRepository;
use App\Directory\Repositories\MysqlCurrencyRepository;
use App\Directory\Repositories\MysqlRegionRepository;
use App\Directory\Repositories\RegionRepository;
use Quicktane\Core\Module\LocalModuleServiceProvider;

class DirectoryServiceProvider extends LocalModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'directory';
    }

    public function register(): void
    {
        $this->app->bind(CountryRepository::class, MysqlCountryRepository::class);
        $this->app->bind(RegionRepository::class, MysqlRegionRepository::class);
        $this->app->bind(CurrencyRepository::class, MysqlCurrencyRepository::class);
        $this->app->bind(CurrencyRateRepository::class, MysqlCurrencyRateRepository::class);

        $this->app->bind(CountryFacadeContract::class, CountryFacade::class);
        $this->app->bind(CurrencyFacadeContract::class, CurrencyFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();
    }
}
