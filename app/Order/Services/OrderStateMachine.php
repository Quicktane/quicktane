<?php

declare(strict_types=1);

namespace App\Order\Services;

use App\Order\Enums\OrderStatus;

class OrderStateMachine
{
    /**
     * @var array<string, array<OrderStatus>>
     */
    private const array TRANSITIONS = [
        'pending' => [OrderStatus::Processing, OrderStatus::OnHold, OrderStatus::Canceled],
        'on_hold' => [OrderStatus::Processing, OrderStatus::Canceled],
        'processing' => [OrderStatus::Shipped, OrderStatus::Canceled, OrderStatus::Refunded],
        'shipped' => [OrderStatus::Delivered, OrderStatus::Returned],
        'delivered' => [OrderStatus::Completed, OrderStatus::Refunded],
        'completed' => [OrderStatus::Refunded],
        'returned' => [OrderStatus::Refunded],
    ];

    /**
     * @return array<OrderStatus>
     */
    public function getAllowedTransitions(OrderStatus $currentStatus): array
    {
        return self::TRANSITIONS[$currentStatus->value] ?? [];
    }

    public function canTransition(OrderStatus $from, OrderStatus $to): bool
    {
        $allowed = $this->getAllowedTransitions($from);

        return in_array($to, $allowed, true);
    }
}
