<?php

declare(strict_types=1);

namespace App\Order\Facades;

use App\Order\Contracts\OrderFacade as OrderFacadeContract;
use App\Order\Models\Order;
use App\Order\Services\OrderService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderFacade implements OrderFacadeContract
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function createOrder(array $data): Order
    {
        return $this->orderService->createOrder($data);
    }

    public function getOrder(int $id): ?Order
    {
        return $this->orderService->getOrder($id);
    }

    public function getOrderByUuid(string $uuid): ?Order
    {
        return $this->orderService->getOrderByUuid($uuid);
    }

    public function getOrderByIncrementId(string $incrementId): ?Order
    {
        return $this->orderService->getOrderByIncrementId($incrementId);
    }

    public function getOrdersByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderService->getOrdersByCustomer($customerId, $perPage);
    }

    public function getOrderWithDetails(int $id): ?Order
    {
        return $this->orderService->getOrderWithDetails($id);
    }
}
