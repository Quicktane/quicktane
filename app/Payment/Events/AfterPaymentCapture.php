<?php

declare(strict_types=1);

namespace App\Payment\Events;

use App\Payment\Models\Transaction;
use Quicktane\Core\Events\OperationContext;

class AfterPaymentCapture
{
    public function __construct(
        public readonly Transaction $transaction,
        public readonly OperationContext $context,
    ) {}
}
