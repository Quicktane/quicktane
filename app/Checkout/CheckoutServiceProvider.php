<?php

declare(strict_types=1);

namespace App\Checkout;

use App\Checkout\Contracts\CheckoutFacade as CheckoutFacadeContract;
use App\Checkout\Facades\CheckoutFacade;
use App\Checkout\Repositories\CheckoutSessionRepository;
use App\Checkout\Repositories\MysqlCheckoutSessionRepository;
use App\Checkout\Steps\CalculateGrandTotalStep;
use App\Checkout\Steps\CalculateSubtotalStep;
use App\Checkout\Steps\CalculateTotalsStep;
use App\Checkout\Steps\ConvertCartStep;
use App\Checkout\Steps\ReserveInventoryStep;
use App\Checkout\Steps\ValidateCartStep;
use App\Checkout\Steps\ValidateStockStep;
use Quicktane\Core\Module\LocalModuleServiceProvider;
use Quicktane\Core\Pipeline\PipelineRegistry;

class CheckoutServiceProvider extends LocalModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'checkout';
    }

    public function register(): void
    {
        $this->app->bind(CheckoutSessionRepository::class, MysqlCheckoutSessionRepository::class);
        $this->app->bind(CheckoutFacadeContract::class, CheckoutFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();

        /** @var PipelineRegistry $pipelineRegistry */
        $pipelineRegistry = $this->app->make(PipelineRegistry::class);

        // checkout.totals pipeline steps
        $pipelineRegistry->register('checkout.totals', CalculateSubtotalStep::class);
        $pipelineRegistry->register('checkout.totals', CalculateGrandTotalStep::class);

        // checkout.place pipeline steps
        $pipelineRegistry->register('checkout.place', ValidateCartStep::class);
        $pipelineRegistry->register('checkout.place', CalculateTotalsStep::class);
        $pipelineRegistry->register('checkout.place', ValidateStockStep::class);
        $pipelineRegistry->register('checkout.place', ReserveInventoryStep::class);
        $pipelineRegistry->register('checkout.place', ConvertCartStep::class);
    }
}
