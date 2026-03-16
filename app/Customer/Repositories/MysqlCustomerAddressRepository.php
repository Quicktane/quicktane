<?php

declare(strict_types=1);

namespace App\Customer\Repositories;

use App\Customer\Models\CustomerAddress;
use Illuminate\Support\Collection;

class MysqlCustomerAddressRepository implements CustomerAddressRepository
{
    public function __construct(
        private readonly CustomerAddress $addressModel,
    ) {}

    public function findById(int $id): ?CustomerAddress
    {
        return $this->addressModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?CustomerAddress
    {
        return $this->addressModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function getByCustomer(int $customerId): Collection
    {
        return $this->addressModel->newQuery()
            ->where('customer_id', $customerId)
            ->get();
    }

    public function create(array $data): CustomerAddress
    {
        return $this->addressModel->newQuery()->create($data);
    }

    public function update(CustomerAddress $address, array $data): CustomerAddress
    {
        $address->update($data);

        return $address;
    }

    public function delete(CustomerAddress $address): bool
    {
        return (bool) $address->delete();
    }

    public function clearDefaultBilling(int $customerId): void
    {
        $this->addressModel->newQuery()
            ->where('customer_id', $customerId)
            ->where('is_default_billing', true)
            ->update(['is_default_billing' => false]);
    }

    public function clearDefaultShipping(int $customerId): void
    {
        $this->addressModel->newQuery()
            ->where('customer_id', $customerId)
            ->where('is_default_shipping', true)
            ->update(['is_default_shipping' => false]);
    }
}
