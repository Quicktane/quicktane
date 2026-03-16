<?php

declare(strict_types=1);

namespace App\User;

use App\User\Contracts\AclFacade as AclFacadeContract;
use App\User\Contracts\AuthFacade as AuthFacadeContract;
use App\User\Facades\AclFacade;
use App\User\Facades\AuthFacade;
use App\User\Repositories\MysqlPermissionRepository;
use App\User\Repositories\MysqlRoleRepository;
use App\User\Repositories\MysqlUserRepository;
use App\User\Repositories\PermissionRepository;
use App\User\Repositories\RoleRepository;
use App\User\Repositories\UserRepository;
use Quicktane\Core\Module\LocalModuleServiceProvider;

class UserServiceProvider extends LocalModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'user';
    }

    public function register(): void
    {
        $this->app->bind(UserRepository::class, MysqlUserRepository::class);
        $this->app->bind(RoleRepository::class, MysqlRoleRepository::class);
        $this->app->bind(PermissionRepository::class, MysqlPermissionRepository::class);

        $this->app->bind(AuthFacadeContract::class, AuthFacade::class);
        $this->app->bind(AclFacadeContract::class, AclFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();
    }
}
