<?php

declare(strict_types=1);

namespace App\Customer\Repositories;

use App\Customer\Models\CustomerGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerGroupRepository
{
    public function findById(int $id): ?CustomerGroup;

    public function findByUuid(string $uuid): ?CustomerGroup;

    public function findByCode(string $code): ?CustomerGroup;

    public function findDefault(): ?CustomerGroup;

    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): CustomerGroup;

    public function update(CustomerGroup $group, array $data): CustomerGroup;

    public function delete(CustomerGroup $group): bool;
}
