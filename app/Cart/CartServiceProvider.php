<?php

declare(strict_types=1);

namespace App\Cart;

use App\Cart\Contracts\CartFacade as CartFacadeContract;
use App\Cart\Facades\CartFacade;
use App\Cart\Repositories\CartItemRepository;
use App\Cart\Repositories\CartRepository;
use App\Cart\Repositories\MysqlCartItemRepository;
use App\Cart\Repositories\MysqlCartRepository;
use Quicktane\Core\Module\LocalModuleServiceProvider;

class CartServiceProvider extends LocalModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'cart';
    }

    public function register(): void
    {
        $this->app->bind(CartRepository::class, MysqlCartRepository::class);
        $this->app->bind(CartItemRepository::class, MysqlCartItemRepository::class);

        $this->app->bind(CartFacadeContract::class, CartFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();
    }
}
