<?php

declare(strict_types=1);

namespace App\Payment;

use App\Payment\Contracts\PaymentFacade as PaymentFacadeContract;
use App\Payment\Facades\PaymentFacade;
use App\Payment\Repositories\MysqlPaymentMethodRepository;
use App\Payment\Repositories\MysqlTransactionLogRepository;
use App\Payment\Repositories\MysqlTransactionRepository;
use App\Payment\Repositories\PaymentMethodRepository;
use App\Payment\Repositories\TransactionLogRepository;
use App\Payment\Repositories\TransactionRepository;
use App\Payment\Services\OfflineGateway;
use App\Payment\Services\PaymentGatewayRegistry;
use App\Payment\Steps\AuthorizePaymentStep;
use Quicktane\Core\Module\LocalModuleServiceProvider;
use Quicktane\Core\Pipeline\PipelineRegistry;

class PaymentServiceProvider extends LocalModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'payment';
    }

    public function register(): void
    {
        $this->app->bind(PaymentMethodRepository::class, MysqlPaymentMethodRepository::class);
        $this->app->bind(TransactionRepository::class, MysqlTransactionRepository::class);
        $this->app->bind(TransactionLogRepository::class, MysqlTransactionLogRepository::class);

        $this->app->bind(PaymentFacadeContract::class, PaymentFacade::class);

        $this->app->singleton(PaymentGatewayRegistry::class, function (): PaymentGatewayRegistry {
            $registry = new PaymentGatewayRegistry;
            $registry->register(new OfflineGateway);

            return $registry;
        });
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();

        /** @var PipelineRegistry $pipelineRegistry */
        $pipelineRegistry = $this->app->make(PipelineRegistry::class);
        $pipelineRegistry->register(AuthorizePaymentStep::pipeline(), AuthorizePaymentStep::class);
    }
}
