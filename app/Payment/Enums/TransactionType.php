<?php

declare(strict_types=1);

namespace App\Payment\Enums;

enum TransactionType: string
{
    case Authorize = 'authorize';
    case Capture = 'capture';
    case Void = 'void';
    case Refund = 'refund';
}
