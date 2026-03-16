<?php

declare(strict_types=1);

namespace App\User\Contracts;

use App\User\Models\Role;
use App\User\Models\User;
use Illuminate\Support\Collection;

interface AclFacade
{
    public function hasPermission(User $user, string $permissionSlug): bool;

    public function listPermissions(): Collection;

    public function getPermissionsForRole(Role $role): Collection;
}
