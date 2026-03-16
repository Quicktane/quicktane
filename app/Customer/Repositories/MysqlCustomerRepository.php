<?php

declare(strict_types=1);

namespace App\Customer\Repositories;

use App\Customer\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MysqlCustomerRepository implements CustomerRepository
{
    public function __construct(
        private readonly Customer $customerModel,
    ) {}

    public function findById(int $id): ?Customer
    {
        return $this->customerModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Customer
    {
        return $this->customerModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByEmail(string $email, int $storeId): ?Customer
    {
        return $this->customerModel->newQuery()
            ->where('email', $email)
            ->where('store_id', $storeId)
            ->first();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->customerModel->newQuery()->with('group');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search): void {
                $query->where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['customer_group_id'])) {
            $query->where('customer_group_id', $filters['customer_group_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Customer
    {
        return $this->customerModel->newQuery()->create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer;
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }
}
