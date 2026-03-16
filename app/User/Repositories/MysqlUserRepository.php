<?php

declare(strict_types=1);

namespace App\User\Repositories;

use App\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MysqlUserRepository implements UserRepository
{
    public function __construct(
        private readonly User $userModel,
    ) {}

    public function findById(int $id): ?User
    {
        return $this->userModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?User
    {
        return $this->userModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userModel->newQuery()->where('email', $email)->first();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userModel->newQuery()->with('role')->paginate($perPage);
    }

    public function create(array $data): User
    {
        return $this->userModel->newQuery()->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}
