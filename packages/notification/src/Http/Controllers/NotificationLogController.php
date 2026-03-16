<?php

declare(strict_types=1);

namespace Quicktane\Notification\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\Notification\Enums\TemplateCode;
use Quicktane\Notification\Http\Resources\NotificationLogResource;
use Quicktane\Notification\Models\NotificationLog;
use Quicktane\Notification\Repositories\NotificationLogRepository;

class NotificationLogController extends Controller
{
    public function __construct(
        private readonly NotificationLogRepository $notificationLogRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['channel', 'status', 'template_code', 'recipient', 'date_from', 'date_to']);

        return NotificationLogResource::collection(
            $this->notificationLogRepository->paginate($filters, $perPage),
        );
    }

    public function show(NotificationLog $notificationLog): NotificationLogResource
    {
        return new NotificationLogResource($notificationLog);
    }

    /**
     * @return array<string, string>
     */
    public function templates(): array
    {
        $templates = [];

        foreach (TemplateCode::cases() as $case) {
            $templates[$case->value] = $case->name;
        }

        return $templates;
    }
}
