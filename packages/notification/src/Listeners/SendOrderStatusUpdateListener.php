<?php

declare(strict_types=1);

namespace Quicktane\Notification\Listeners;

use App\Order\Events\AfterOrderStatusChange;
use Quicktane\Notification\Enums\TemplateCode;
use Quicktane\Notification\Services\NotificationService;

class SendOrderStatusUpdateListener
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function handle(AfterOrderStatusChange $event): void
    {
        $this->notificationService->send(
            templateCode: TemplateCode::OrderStatusChange,
            recipient: $event->order->customer_email,
            data: [
                'order' => $event->order,
                'from_status' => $event->fromStatus->value,
                'to_status' => $event->toStatus->value,
            ],
            storeViewId: (int) $event->order->store_id,
        );
    }
}
