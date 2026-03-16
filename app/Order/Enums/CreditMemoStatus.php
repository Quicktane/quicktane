<?php

declare(strict_types=1);

namespace App\Order\Enums;

enum CreditMemoStatus: string
{
    case Pending = 'pending';
    case Refunded = 'refunded';
}
