<?php

declare(strict_types=1);

namespace Quicktane\Notification\Enums;

enum NotificationChannel: string
{
    case Email = 'email';
    case Sms = 'sms';
}
