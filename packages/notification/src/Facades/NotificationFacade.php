<?php

declare(strict_types=1);

namespace Quicktane\Notification\Facades;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Notification\Contracts\NotificationFacade as NotificationFacadeContract;
use Quicktane\Notification\Enums\TemplateCode;
use Quicktane\Notification\Models\NotificationLog;
use Quicktane\Notification\Repositories\NotificationLogRepository;
use Quicktane\Notification\Services\NotificationService;

class NotificationFacade implements NotificationFacadeContract
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly NotificationLogRepository $notificationLogRepository,
    ) {}

    public function send(TemplateCode $templateCode, string $recipient, array $data, int $storeViewId = 0): NotificationLog
    {
        return $this->notificationService->send($templateCode, $recipient, $data, $storeViewId);
    }

    public function getLog(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->notificationLogRepository->paginate($filters, $perPage);
    }
}
