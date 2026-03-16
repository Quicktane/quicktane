<?php

declare(strict_types=1);

namespace Quicktane\Shipping;

use Quicktane\Core\Module\ModuleServiceProvider;
use Quicktane\Core\Pipeline\PipelineRegistry;
use Quicktane\Shipping\Contracts\ShippingFacade as ShippingFacadeContract;
use Quicktane\Shipping\Facades\ShippingFacade;
use Quicktane\Shipping\Repositories\MysqlShippingMethodRepository;
use Quicktane\Shipping\Repositories\MysqlShippingRateRepository;
use Quicktane\Shipping\Repositories\MysqlShippingZoneRepository;
use Quicktane\Shipping\Repositories\ShippingMethodRepository;
use Quicktane\Shipping\Repositories\ShippingRateRepository;
use Quicktane\Shipping\Repositories\ShippingZoneRepository;
use Quicktane\Shipping\Services\FlatRateCarrier;
use Quicktane\Shipping\Services\ShippingCarrierRegistry;
use Quicktane\Shipping\Steps\CalculateShippingStep;

class ShippingServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'shipping';
    }

    public function register(): void
    {
        $this->app->bind(ShippingMethodRepository::class, MysqlShippingMethodRepository::class);
        $this->app->bind(ShippingZoneRepository::class, MysqlShippingZoneRepository::class);
        $this->app->bind(ShippingRateRepository::class, MysqlShippingRateRepository::class);

        $this->app->bind(ShippingFacadeContract::class, ShippingFacade::class);

        $this->app->singleton(ShippingCarrierRegistry::class, function ($app): ShippingCarrierRegistry {
            $shippingCarrierRegistry = new ShippingCarrierRegistry;
            $shippingCarrierRegistry->register($app->make(FlatRateCarrier::class));

            return $shippingCarrierRegistry;
        });
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();

        $pipelineRegistry = $this->app->make(PipelineRegistry::class);
        $pipelineRegistry->register('checkout.totals', CalculateShippingStep::class);
    }
}
