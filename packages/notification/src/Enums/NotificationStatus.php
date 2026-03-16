<?php

declare(strict_types=1);

namespace Quicktane\Notification\Enums;

enum NotificationStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
}
