<?php

declare(strict_types=1);

namespace App\Customer\Facades;

use App\Customer\Contracts\CustomerGroupFacade as CustomerGroupFacadeContract;
use App\Customer\Models\CustomerGroup;
use App\Customer\Repositories\CustomerGroupRepository;
use Illuminate\Support\Collection;

class CustomerGroupFacade implements CustomerGroupFacadeContract
{
    public function __construct(
        private readonly CustomerGroupRepository $customerGroupRepository,
    ) {}

    public function findGroup(int $id): ?CustomerGroup
    {
        return $this->customerGroupRepository->findById($id);
    }

    public function findGroupByCode(string $code): ?CustomerGroup
    {
        return $this->customerGroupRepository->findByCode($code);
    }

    public function getDefaultGroup(): CustomerGroup
    {
        $group = $this->customerGroupRepository->findDefault();

        if ($group === null) {
            throw new \RuntimeException('No default customer group configured.');
        }

        return $group;
    }

    public function listGroups(): Collection
    {
        return $this->customerGroupRepository->all();
    }
}
