<?php

declare(strict_types=1);

namespace App\Order\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case OnHold = 'on_hold';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Completed = 'completed';
    case Canceled = 'canceled';
    case Returned = 'returned';
    case Refunded = 'refunded';
}
