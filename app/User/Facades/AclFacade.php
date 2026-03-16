<?php

declare(strict_types=1);

namespace App\User\Facades;

use App\User\Contracts\AclFacade as AclFacadeContract;
use App\User\Models\Role;
use App\User\Models\User;
use App\User\Services\AclService;
use Illuminate\Support\Collection;

class AclFacade implements AclFacadeContract
{
    public function __construct(
        private readonly AclService $aclService,
    ) {}

    public function hasPermission(User $user, string $permissionSlug): bool
    {
        return $this->aclService->hasPermission($user, $permissionSlug);
    }

    public function listPermissions(): Collection
    {
        return $this->aclService->allPermissions();
    }

    public function getPermissionsForRole(Role $role): Collection
    {
        return $this->aclService->getPermissionsForRole($role);
    }
}
