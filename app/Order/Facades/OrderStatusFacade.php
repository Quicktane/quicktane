<?php

declare(strict_types=1);

namespace App\Order\Facades;

use App\Order\Contracts\OrderStatusFacade as OrderStatusFacadeContract;
use App\Order\Enums\OrderStatus;
use App\Order\Models\Order;
use App\Order\Services\OrderStatusService;
use Illuminate\Support\Collection;

class OrderStatusFacade implements OrderStatusFacadeContract
{
    public function __construct(
        private readonly OrderStatusService $orderStatusService,
    ) {}

    public function changeStatus(int $orderId, OrderStatus $newStatus, ?string $comment = null, ?int $userId = null, bool $notifyCustomer = false): Order
    {
        return $this->orderStatusService->changeStatus($orderId, $newStatus, $comment, $userId, $notifyCustomer);
    }

    public function getAllowedTransitions(OrderStatus $currentStatus): array
    {
        return $this->orderStatusService->getAllowedTransitions($currentStatus);
    }

    public function getHistory(int $orderId): Collection
    {
        return $this->orderStatusService->getHistory($orderId);
    }
}
