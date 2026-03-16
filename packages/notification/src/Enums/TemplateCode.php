<?php

declare(strict_types=1);

namespace Quicktane\Notification\Enums;

enum TemplateCode: string
{
    case OrderConfirmation = 'order_confirmation';
    case OrderStatusChange = 'order_status_change';
    case CustomerWelcome = 'customer_welcome';
    case CustomerPasswordReset = 'customer_password_reset';
}
