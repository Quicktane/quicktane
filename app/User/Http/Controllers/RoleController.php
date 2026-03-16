<?php

declare(strict_types=1);

namespace App\User\Http\Controllers;

use App\User\Http\Requests\StoreRoleRequest;
use App\User\Http\Requests\UpdateRoleRequest;
use App\User\Http\Resources\PermissionResource;
use App\User\Http\Resources\RoleResource;
use App\User\Models\Role;
use App\User\Repositories\PermissionRepository;
use App\User\Repositories\RoleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
        private readonly PermissionRepository $permissionRepository,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $roles = $this->roleRepository->all();

        return RoleResource::collection($roles);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $permissionIds = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = $this->roleRepository->create($data);

        if (! empty($permissionIds)) {
            $this->roleRepository->syncPermissions($role, $permissionIds);
        }

        $role->load('permissions');

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Role $role): RoleResource
    {
        $role->load('permissions');

        return new RoleResource($role);
    }

    public function update(UpdateRoleRequest $request, Role $role): RoleResource
    {
        $data = $request->validated();
        $permissionIds = $data['permissions'] ?? null;
        unset($data['permissions']);

        $role = $this->roleRepository->update($role, $data);

        if ($permissionIds !== null) {
            $this->roleRepository->syncPermissions($role, $permissionIds);
        }

        $role->load('permissions');

        return new RoleResource($role);
    }

    public function destroy(Role $role): JsonResponse
    {
        if ($role->is_system) {
            return response()->json([
                'message' => 'System roles cannot be deleted.',
            ], 403);
        }

        $this->roleRepository->delete($role);

        return response()->json(null, 204);
    }

    public function permissions(): AnonymousResourceCollection
    {
        $permissions = $this->permissionRepository->all();

        return PermissionResource::collection($permissions);
    }
}
