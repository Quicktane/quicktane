<?php

declare(strict_types=1);

namespace App\Payment\Events;

use Quicktane\Core\Events\OperationContext;

class BeforePaymentAuthorize
{
    public function __construct(
        public readonly OperationContext $context,
    ) {}
}
