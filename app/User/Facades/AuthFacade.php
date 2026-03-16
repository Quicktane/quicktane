<?php

declare(strict_types=1);

namespace App\User\Facades;

use App\User\Contracts\AuthFacade as AuthFacadeContract;
use App\User\Models\User;
use App\User\Services\AclService;
use App\User\Services\AuthService;

class AuthFacade implements AuthFacadeContract
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly AclService $aclService,
    ) {}

    public function authenticate(string $email, string $password): array
    {
        return $this->authService->login($email, $password);
    }

    public function currentUser(User $user): User
    {
        return $this->authService->getCurrentUser($user);
    }

    public function logout(User $user): void
    {
        $this->authService->logout($user);
    }

    public function checkPermission(User $user, string $permissionSlug): bool
    {
        return $this->aclService->hasPermission($user, $permissionSlug);
    }
}
