<?php

declare(strict_types=1);

namespace Quicktane\Notification\Listeners;

use App\Customer\Events\AfterCustomerRegister;
use Quicktane\Notification\Enums\TemplateCode;
use Quicktane\Notification\Services\NotificationService;

class SendCustomerWelcomeListener
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function handle(AfterCustomerRegister $event): void
    {
        $this->notificationService->send(
            templateCode: TemplateCode::CustomerWelcome,
            recipient: $event->customer->email,
            data: ['customer' => $event->customer],
            storeViewId: (int) $event->customer->store_id,
        );
    }
}
