<?php

declare(strict_types=1);

namespace App\User\Repositories;

use App\User\Models\Permission;
use App\User\Models\Role;
use Illuminate\Support\Collection;

interface PermissionRepository
{
    public function all(): Collection;

    public function findBySlug(string $slug): ?Permission;

    public function getByModule(string $module): Collection;

    public function getForRole(Role $role): Collection;
}
