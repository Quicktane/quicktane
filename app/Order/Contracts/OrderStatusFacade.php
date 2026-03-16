<?php

declare(strict_types=1);

namespace App\Order\Contracts;

use App\Order\Enums\OrderStatus;
use App\Order\Models\Order;
use Illuminate\Support\Collection;

interface OrderStatusFacade
{
    public function changeStatus(int $orderId, OrderStatus $newStatus, ?string $comment = null, ?int $userId = null, bool $notifyCustomer = false): Order;

    /**
     * @return array<OrderStatus>
     */
    public function getAllowedTransitions(OrderStatus $currentStatus): array;

    public function getHistory(int $orderId): Collection;
}
