<?php

declare(strict_types=1);

namespace App\Customer\Facades;

use App\Customer\Contracts\CustomerFacade as CustomerFacadeContract;
use App\Customer\Models\Customer;
use App\Customer\Models\CustomerGroup;
use App\Customer\Repositories\CustomerGroupRepository;
use App\Customer\Repositories\CustomerRepository;

class CustomerFacade implements CustomerFacadeContract
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly CustomerGroupRepository $customerGroupRepository,
    ) {}

    public function findCustomer(int $id): ?Customer
    {
        return $this->customerRepository->findById($id);
    }

    public function findCustomerByUuid(string $uuid): ?Customer
    {
        return $this->customerRepository->findByUuid($uuid);
    }

    public function findCustomerByEmail(string $email, int $storeId): ?Customer
    {
        return $this->customerRepository->findByEmail($email, $storeId);
    }

    public function getCustomerWithAddresses(int $id): ?Customer
    {
        $customer = $this->customerRepository->findById($id);

        if ($customer !== null) {
            $customer->load('addresses', 'group');
        }

        return $customer;
    }

    public function getCustomerGroup(int $customerId): ?CustomerGroup
    {
        $customer = $this->customerRepository->findById($customerId);

        if ($customer === null) {
            return null;
        }

        return $customer->group;
    }

    public function isInGroup(int $customerId, string $groupCode): bool
    {
        $customer = $this->customerRepository->findById($customerId);

        if ($customer === null) {
            return false;
        }

        $group = $this->customerGroupRepository->findById($customer->customer_group_id);

        return $group !== null && $group->code === $groupCode;
    }
}
