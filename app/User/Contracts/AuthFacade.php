<?php

declare(strict_types=1);

namespace App\User\Contracts;

use App\User\Models\User;

interface AuthFacade
{
    /**
     * @return array{token: string, user: User}
     */
    public function authenticate(string $email, string $password): array;

    public function currentUser(User $user): User;

    public function logout(User $user): void;

    public function checkPermission(User $user, string $permissionSlug): bool;
}
