<?php

declare(strict_types=1);

namespace App\Catalog;

use App\Catalog\Contracts\AttributeFacade as AttributeFacadeContract;
use App\Catalog\Contracts\CategoryFacade as CategoryFacadeContract;
use App\Catalog\Contracts\PricingFacade as PricingFacadeContract;
use App\Catalog\Contracts\ProductFacade as ProductFacadeContract;
use App\Catalog\Facades\AttributeFacade;
use App\Catalog\Facades\CategoryFacade;
use App\Catalog\Facades\PricingFacade;
use App\Catalog\Facades\ProductFacade;
use App\Catalog\Repositories\AttributeOptionRepository;
use App\Catalog\Repositories\AttributeRepository;
use App\Catalog\Repositories\AttributeSetRepository;
use App\Catalog\Repositories\AttributeValueRepository;
use App\Catalog\Repositories\CategoryRepository;
use App\Catalog\Repositories\MysqlAttributeOptionRepository;
use App\Catalog\Repositories\MysqlAttributeRepository;
use App\Catalog\Repositories\MysqlAttributeSetRepository;
use App\Catalog\Repositories\MysqlAttributeValueRepository;
use App\Catalog\Repositories\MysqlCategoryRepository;
use App\Catalog\Repositories\MysqlProductRepository;
use App\Catalog\Repositories\ProductRepository;
use Quicktane\Core\Module\LocalModuleServiceProvider;

class CatalogServiceProvider extends LocalModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'catalog';
    }

    public function register(): void
    {
        $this->app->bind(AttributeRepository::class, MysqlAttributeRepository::class);
        $this->app->bind(AttributeOptionRepository::class, MysqlAttributeOptionRepository::class);
        $this->app->bind(AttributeSetRepository::class, MysqlAttributeSetRepository::class);
        $this->app->bind(CategoryRepository::class, MysqlCategoryRepository::class);
        $this->app->bind(ProductRepository::class, MysqlProductRepository::class);
        $this->app->bind(AttributeValueRepository::class, MysqlAttributeValueRepository::class);

        $this->app->bind(AttributeFacadeContract::class, AttributeFacade::class);
        $this->app->bind(CategoryFacadeContract::class, CategoryFacade::class);
        $this->app->bind(ProductFacadeContract::class, ProductFacade::class);
        $this->app->bind(PricingFacadeContract::class, PricingFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();
    }
}
