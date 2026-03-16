<?php

declare(strict_types=1);

namespace App\Customer\Events;

use App\Customer\Models\Customer;
use Quicktane\Core\Events\OperationContext;

class AfterCustomerUpdate
{
    public function __construct(
        public readonly Customer $customer,
        public readonly OperationContext $context,
    ) {}
}
