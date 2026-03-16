<?php

declare(strict_types=1);

namespace App\Order\Listeners;

use App\Order\Enums\OrderStatus;
use App\Order\Events\AfterOrderStatusChange;
use Quicktane\Inventory\Contracts\ReservationFacade;

class ReleaseInventoryOnCancel
{
    public function __construct(
        private readonly ReservationFacade $reservationFacade,
    ) {}

    public function handle(AfterOrderStatusChange $event): void
    {
        if ($event->toStatus !== OrderStatus::Canceled) {
            return;
        }

        $order = $event->order;
        $order->load('items');

        foreach ($order->items as $orderItem) {
            $this->reservationFacade->release($orderItem->product_id, 1, $orderItem->quantity);
        }
    }
}
