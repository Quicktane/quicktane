<?php

declare(strict_types=1);

namespace App\Customer\Events;

use Quicktane\Core\Events\OperationContext;

class BeforeCustomerRegister
{
    public function __construct(
        public readonly OperationContext $context,
    ) {}
}
