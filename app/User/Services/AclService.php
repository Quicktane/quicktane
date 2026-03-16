<?php

declare(strict_types=1);

namespace App\User\Services;

use App\User\Models\Role;
use App\User\Models\User;
use App\User\Repositories\PermissionRepository;
use Illuminate\Support\Collection;

class AclService
{
    public function __construct(
        private readonly PermissionRepository $permissionRepository,
    ) {}

    public function hasPermission(User $user, string $permissionSlug): bool
    {
        $role = $user->role;

        if (! $role) {
            return false;
        }

        if ($role->is_system) {
            return true;
        }

        return $role->permissions->contains('slug', $permissionSlug);
    }

    public function getPermissionsForRole(Role $role): Collection
    {
        return $this->permissionRepository->getForRole($role);
    }

    public function allPermissions(): Collection
    {
        return $this->permissionRepository->all();
    }
}
