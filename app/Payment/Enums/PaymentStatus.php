<?php

declare(strict_types=1);

namespace App\Payment\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Authorized = 'authorized';
    case Captured = 'captured';
    case Voided = 'voided';
    case Refunded = 'refunded';
    case Failed = 'failed';
    case PartiallyRefunded = 'partially_refunded';
}
