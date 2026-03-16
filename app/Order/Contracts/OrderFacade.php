<?php

declare(strict_types=1);

namespace App\Order\Contracts;

use App\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderFacade
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data): Order;

    public function getOrder(int $id): ?Order;

    public function getOrderByUuid(string $uuid): ?Order;

    public function getOrderByIncrementId(string $incrementId): ?Order;

    public function getOrdersByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator;

    public function getOrderWithDetails(int $id): ?Order;
}
