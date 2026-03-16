<?php

declare(strict_types=1);

namespace App\User\Repositories;

use App\User\Models\Permission;
use App\User\Models\Role;
use Illuminate\Support\Collection;

class MysqlPermissionRepository implements PermissionRepository
{
    public function __construct(
        private readonly Permission $permissionModel,
    ) {}

    public function all(): Collection
    {
        return $this->permissionModel->newQuery()->get();
    }

    public function findBySlug(string $slug): ?Permission
    {
        return $this->permissionModel->newQuery()->where('slug', $slug)->first();
    }

    public function getByModule(string $module): Collection
    {
        return $this->permissionModel->newQuery()->where('module', $module)->get();
    }

    public function getForRole(Role $role): Collection
    {
        return $role->permissions;
    }
}
