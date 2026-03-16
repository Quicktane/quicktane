<?php

declare(strict_types=1);

namespace Quicktane\Notification\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Notification\Models\NotificationLog;

interface NotificationLogRepository
{
    public function findById(int $id): ?NotificationLog;

    public function findByUuid(string $uuid): ?NotificationLog;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): NotificationLog;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(NotificationLog $log, array $data): NotificationLog;
}
