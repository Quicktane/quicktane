<?php

declare(strict_types=1);

namespace App\Customer\Events;

use App\Customer\Models\Customer;

class AfterCustomerLogin
{
    public function __construct(
        public readonly Customer $customer,
    ) {}
}
