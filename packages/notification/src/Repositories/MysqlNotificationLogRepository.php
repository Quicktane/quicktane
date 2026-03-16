<?php

declare(strict_types=1);

namespace Quicktane\Notification\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Notification\Models\NotificationLog;

class MysqlNotificationLogRepository implements NotificationLogRepository
{
    public function __construct(
        private readonly NotificationLog $notificationLogModel,
    ) {}

    public function findById(int $id): ?NotificationLog
    {
        return $this->notificationLogModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?NotificationLog
    {
        return $this->notificationLogModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->notificationLogModel->newQuery();

        if (isset($filters['channel'])) {
            $query->where('channel', $filters['channel']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['template_code'])) {
            $query->where('template_code', $filters['template_code']);
        }

        if (isset($filters['recipient'])) {
            $query->where('recipient', 'like', "%{$filters['recipient']}%");
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): NotificationLog
    {
        return $this->notificationLogModel->newQuery()->create($data);
    }

    public function update(NotificationLog $log, array $data): NotificationLog
    {
        $log->update($data);

        return $log;
    }
}
