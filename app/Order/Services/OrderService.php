<?php

declare(strict_types=1);

namespace App\Order\Services;

use App\Order\Models\Order;
use App\Order\Repositories\OrderItemRepository;
use App\Order\Repositories\OrderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Core\Trace\OperationTracer;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderItemRepository $orderItemRepository,
        private readonly IncrementIdGenerator $incrementIdGenerator,
        private readonly OperationTracer $operationTracer,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data): Order
    {
        return $this->operationTracer->execute('order.create', function () use ($data): Order {
            $items = $data['items'] ?? [];
            unset($data['items']);

            $data['increment_id'] = $this->incrementIdGenerator->nextOrderId();

            $order = $this->orderRepository->create($data);

            foreach ($items as $itemData) {
                $itemData['order_id'] = $order->id;
                $this->orderItemRepository->create($itemData);
            }

            $order->load('items');

            return $order;
        });
    }

    public function getOrder(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function getOrderByUuid(string $uuid): ?Order
    {
        return $this->orderRepository->findByUuid($uuid);
    }

    public function getOrderByIncrementId(string $incrementId): ?Order
    {
        return $this->orderRepository->findByIncrementId($incrementId);
    }

    public function getOrdersByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->findByCustomer($customerId, $perPage);
    }

    public function getOrderWithDetails(int $id): ?Order
    {
        $order = $this->orderRepository->findById($id);

        if ($order !== null) {
            $order->load(['items', 'addresses', 'history', 'invoices.items', 'creditMemos.items', 'customer']);
        }

        return $order;
    }
}
