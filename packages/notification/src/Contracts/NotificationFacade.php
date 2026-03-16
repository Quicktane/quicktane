<?php

declare(strict_types=1);

namespace Quicktane\Notification\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Notification\Enums\TemplateCode;
use Quicktane\Notification\Models\NotificationLog;

interface NotificationFacade
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function send(TemplateCode $templateCode, string $recipient, array $data, int $storeViewId = 0): NotificationLog;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getLog(array $filters, int $perPage = 15): LengthAwarePaginator;
}
