<?php

declare(strict_types=1);

namespace App\Order\Events;

use App\Order\Enums\OrderStatus;
use App\Order\Models\Order;

class BeforeOrderStatusChange
{
    public function __construct(
        public readonly Order $order,
        public readonly OrderStatus $fromStatus,
        public readonly OrderStatus $toStatus,
    ) {}
}
