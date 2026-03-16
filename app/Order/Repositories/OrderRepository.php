<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepository
{
    public function findById(int $id): ?Order;

    public function findByUuid(string $uuid): ?Order;

    public function findByIncrementId(string $incrementId): ?Order;

    public function findByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator;

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;
}
