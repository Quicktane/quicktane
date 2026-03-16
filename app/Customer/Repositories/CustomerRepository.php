<?php

declare(strict_types=1);

namespace App\Customer\Repositories;

use App\Customer\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepository
{
    public function findById(int $id): ?Customer;

    public function findByUuid(string $uuid): ?Customer;

    public function findByEmail(string $email, int $storeId): ?Customer;

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Customer;

    public function update(Customer $customer, array $data): Customer;

    public function delete(Customer $customer): bool;
}
