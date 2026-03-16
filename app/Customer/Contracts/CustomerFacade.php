<?php

declare(strict_types=1);

namespace App\Customer\Contracts;

use App\Customer\Models\Customer;
use App\Customer\Models\CustomerGroup;

interface CustomerFacade
{
    public function findCustomer(int $id): ?Customer;

    public function findCustomerByUuid(string $uuid): ?Customer;

    public function findCustomerByEmail(string $email, int $storeId): ?Customer;

    public function getCustomerWithAddresses(int $id): ?Customer;

    public function getCustomerGroup(int $customerId): ?CustomerGroup;

    public function isInGroup(int $customerId, string $groupCode): bool;
}
