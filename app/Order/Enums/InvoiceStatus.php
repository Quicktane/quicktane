<?php

declare(strict_types=1);

namespace App\Order\Enums;

enum InvoiceStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
}
