<?php

declare(strict_types=1);

namespace App\User\Repositories;

use App\User\Models\Role;
use Illuminate\Support\Collection;

interface RoleRepository
{
    public function findById(int $id): ?Role;

    public function findBySlug(string $slug): ?Role;

    public function all(): Collection;

    public function create(array $data): Role;

    public function update(Role $role, array $data): Role;

    public function delete(Role $role): bool;

    public function syncPermissions(Role $role, array $permissionIds): void;
}
