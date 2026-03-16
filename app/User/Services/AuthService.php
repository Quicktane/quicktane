<?php

declare(strict_types=1);

namespace App\User\Services;

use App\User\Models\User;
use App\User\Repositories\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    /**
     * @return array{token: string, user: User}
     *
     * @throws AuthenticationException
     */
    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (! $user->is_active) {
            throw new AuthenticationException('Account is deactivated.');
        }

        $token = $user->createToken('admin-token')->plainTextToken;

        $user->last_login_at = now();
        $user->save();

        $user->load('role.permissions');

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function getCurrentUser(User $user): User
    {
        $user->load('role.permissions');

        return $user;
    }
}
