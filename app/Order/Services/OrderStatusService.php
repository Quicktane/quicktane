<?php

declare(strict_types=1);

namespace App\Order\Services;

use App\Order\Enums\OrderStatus;
use App\Order\Events\AfterOrderStatusChange;
use App\Order\Events\BeforeOrderStatusChange;
use App\Order\Models\Order;
use App\Order\Repositories\OrderHistoryRepository;
use App\Order\Repositories\OrderRepository;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Quicktane\Core\Events\EventDispatcher;
use Quicktane\Core\Trace\OperationTracer;

class OrderStatusService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderHistoryRepository $orderHistoryRepository,
        private readonly OrderStateMachine $orderStateMachine,
        private readonly EventDispatcher $eventDispatcher,
        private readonly OperationTracer $operationTracer,
    ) {}

    public function changeStatus(int $orderId, OrderStatus $newStatus, ?string $comment = null, ?int $userId = null, bool $notifyCustomer = false): Order
    {
        return $this->operationTracer->execute('order.status.change', function () use ($orderId, $newStatus, $comment, $userId, $notifyCustomer): Order {
            $order = $this->orderRepository->findById($orderId);

            if ($order === null) {
                throw ValidationException::withMessages([
                    'order' => ['Order not found.'],
                ]);
            }

            $currentStatus = $order->status;

            if (! $this->orderStateMachine->canTransition($currentStatus, $newStatus)) {
                throw ValidationException::withMessages([
                    'status' => ["Cannot transition from '{$currentStatus->value}' to '{$newStatus->value}'."],
                ]);
            }

            $this->eventDispatcher->dispatch(new BeforeOrderStatusChange($order, $currentStatus, $newStatus));

            $order = $this->orderRepository->update($order, ['status' => $newStatus]);

            $this->orderHistoryRepository->create([
                'order_id' => $orderId,
                'status' => $newStatus->value,
                'comment' => $comment,
                'is_customer_notified' => $notifyCustomer,
                'user_id' => $userId,
            ]);

            $this->eventDispatcher->dispatch(new AfterOrderStatusChange($order, $currentStatus, $newStatus));

            return $order;
        });
    }

    /**
     * @return array<OrderStatus>
     */
    public function getAllowedTransitions(OrderStatus $currentStatus): array
    {
        return $this->orderStateMachine->getAllowedTransitions($currentStatus);
    }

    public function getHistory(int $orderId): Collection
    {
        return $this->orderHistoryRepository->findByOrder($orderId);
    }
}
