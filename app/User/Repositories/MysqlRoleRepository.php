<?php

declare(strict_types=1);

namespace App\User\Repositories;

use App\User\Models\Role;
use Illuminate\Support\Collection;

class MysqlRoleRepository implements RoleRepository
{
    public function __construct(
        private readonly Role $roleModel,
    ) {}

    public function findById(int $id): ?Role
    {
        return $this->roleModel->newQuery()->find($id);
    }

    public function findBySlug(string $slug): ?Role
    {
        return $this->roleModel->newQuery()->where('slug', $slug)->first();
    }

    public function all(): Collection
    {
        return $this->roleModel->newQuery()->get();
    }

    public function create(array $data): Role
    {
        return $this->roleModel->newQuery()->create($data);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role;
    }

    public function delete(Role $role): bool
    {
        return (bool) $role->delete();
    }

    public function syncPermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
    }
}
