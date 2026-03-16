<?php

declare(strict_types=1);

namespace Quicktane\Notification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Quicktane\Notification\Enums\NotificationChannel;
use Quicktane\Notification\Enums\NotificationStatus;

class NotificationLog extends Model
{
    protected $table = 'notification_logs';

    protected $fillable = [
        'uuid',
        'channel',
        'template_code',
        'recipient',
        'subject',
        'body',
        'variables',
        'status',
        'error_message',
        'store_view_id',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'channel' => NotificationChannel::class,
            'status' => NotificationStatus::class,
            'variables' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (NotificationLog $log): void {
            if (empty($log->uuid)) {
                $log->uuid = (string) Str::uuid();
            }
        });
    }
}
