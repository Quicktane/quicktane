<?php

declare(strict_types=1);

namespace App\Customer\Repositories;

use App\Customer\Models\CustomerGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MysqlCustomerGroupRepository implements CustomerGroupRepository
{
    public function __construct(
        private readonly CustomerGroup $groupModel,
    ) {}

    public function findById(int $id): ?CustomerGroup
    {
        return $this->groupModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?CustomerGroup
    {
        return $this->groupModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCode(string $code): ?CustomerGroup
    {
        return $this->groupModel->newQuery()->where('code', $code)->first();
    }

    public function findDefault(): ?CustomerGroup
    {
        return $this->groupModel->newQuery()->where('is_default', true)->first();
    }

    public function all(): Collection
    {
        return $this->groupModel->newQuery()->orderBy('sort_order')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->groupModel->newQuery()->orderBy('sort_order')->paginate($perPage);
    }

    public function create(array $data): CustomerGroup
    {
        return $this->groupModel->newQuery()->create($data);
    }

    public function update(CustomerGroup $group, array $data): CustomerGroup
    {
        $group->update($data);

        return $group;
    }

    public function delete(CustomerGroup $group): bool
    {
        return (bool) $group->delete();
    }
}
