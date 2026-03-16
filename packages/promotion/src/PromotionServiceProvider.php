<?php

declare(strict_types=1);

namespace Quicktane\Promotion;

use Quicktane\Core\Module\ModuleServiceProvider;
use Quicktane\Core\Pipeline\PipelineRegistry;
use Quicktane\Promotion\Contracts\PromotionFacade as PromotionFacadeContract;
use Quicktane\Promotion\Facades\PromotionFacade;
use Quicktane\Promotion\Repositories\CartPriceRuleRepository;
use Quicktane\Promotion\Repositories\CouponRepository;
use Quicktane\Promotion\Repositories\CouponUsageRepository;
use Quicktane\Promotion\Repositories\MysqlCartPriceRuleRepository;
use Quicktane\Promotion\Repositories\MysqlCouponRepository;
use Quicktane\Promotion\Repositories\MysqlCouponUsageRepository;
use Quicktane\Promotion\Repositories\MysqlRuleAppliedHistoryRepository;
use Quicktane\Promotion\Repositories\MysqlRuleConditionRepository;
use Quicktane\Promotion\Repositories\RuleAppliedHistoryRepository;
use Quicktane\Promotion\Repositories\RuleConditionRepository;
use Quicktane\Promotion\Steps\ApplyDiscountStep;
use Quicktane\Promotion\Steps\RecordPromotionUsageStep;

class PromotionServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'promotion';
    }

    public function register(): void
    {
        $this->app->bind(CartPriceRuleRepository::class, MysqlCartPriceRuleRepository::class);
        $this->app->bind(CouponRepository::class, MysqlCouponRepository::class);
        $this->app->bind(CouponUsageRepository::class, MysqlCouponUsageRepository::class);
        $this->app->bind(RuleConditionRepository::class, MysqlRuleConditionRepository::class);
        $this->app->bind(RuleAppliedHistoryRepository::class, MysqlRuleAppliedHistoryRepository::class);

        $this->app->bind(PromotionFacadeContract::class, PromotionFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();

        /** @var PipelineRegistry $pipelineRegistry */
        $pipelineRegistry = $this->app->make(PipelineRegistry::class);
        $pipelineRegistry->register('checkout.totals', ApplyDiscountStep::class);
        $pipelineRegistry->register('checkout.place', RecordPromotionUsageStep::class);
    }
}
