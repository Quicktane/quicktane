<?php

declare(strict_types=1);

namespace Quicktane\Notification\Listeners;

use App\Order\Enums\OrderStatus;
use App\Order\Events\AfterOrderStatusChange;
use Quicktane\Notification\Enums\TemplateCode;
use Quicktane\Notification\Services\NotificationService;

class SendOrderConfirmationListener
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function handle(AfterOrderStatusChange $event): void
    {
        if ($event->toStatus !== OrderStatus::Processing) {
            return;
        }

        if ($event->fromStatus !== OrderStatus::Pending) {
            return;
        }

        $this->notificationService->send(
            templateCode: TemplateCode::OrderConfirmation,
            recipient: $event->order->customer_email,
            data: ['order' => $event->order],
            storeViewId: (int) $event->order->store_id,
        );
    }
}
