<?php

declare(strict_types=1);

namespace Quicktane\Tax;

use Quicktane\Core\Module\ModuleServiceProvider;
use Quicktane\Core\Pipeline\PipelineRegistry;
use Quicktane\Tax\Contracts\TaxFacade as TaxFacadeContract;
use Quicktane\Tax\Facades\TaxFacade;
use Quicktane\Tax\Repositories\MysqlTaxClassRepository;
use Quicktane\Tax\Repositories\MysqlTaxRateRepository;
use Quicktane\Tax\Repositories\MysqlTaxRuleRepository;
use Quicktane\Tax\Repositories\MysqlTaxZoneRepository;
use Quicktane\Tax\Repositories\TaxClassRepository;
use Quicktane\Tax\Repositories\TaxRateRepository;
use Quicktane\Tax\Repositories\TaxRuleRepository;
use Quicktane\Tax\Repositories\TaxZoneRepository;
use Quicktane\Tax\Steps\CalculateTaxStep;

class TaxServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'tax';
    }

    public function register(): void
    {
        $this->app->bind(TaxClassRepository::class, MysqlTaxClassRepository::class);
        $this->app->bind(TaxZoneRepository::class, MysqlTaxZoneRepository::class);
        $this->app->bind(TaxRateRepository::class, MysqlTaxRateRepository::class);
        $this->app->bind(TaxRuleRepository::class, MysqlTaxRuleRepository::class);

        $this->app->bind(TaxFacadeContract::class, TaxFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();

        /** @var PipelineRegistry $pipelineRegistry */
        $pipelineRegistry = $this->app->make(PipelineRegistry::class);
        $pipelineRegistry->register('checkout.totals', CalculateTaxStep::class);
    }
}
