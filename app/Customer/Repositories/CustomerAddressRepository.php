<?php

declare(strict_types=1);

namespace App\Customer\Repositories;

use App\Customer\Models\CustomerAddress;
use Illuminate\Support\Collection;

interface CustomerAddressRepository
{
    public function findById(int $id): ?CustomerAddress;

    public function findByUuid(string $uuid): ?CustomerAddress;

    public function getByCustomer(int $customerId): Collection;

    public function create(array $data): CustomerAddress;

    public function update(CustomerAddress $address, array $data): CustomerAddress;

    public function delete(CustomerAddress $address): bool;

    public function clearDefaultBilling(int $customerId): void;

    public function clearDefaultShipping(int $customerId): void;
}
