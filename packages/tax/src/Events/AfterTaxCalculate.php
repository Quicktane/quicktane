<?php

declare(strict_types=1);

namespace Quicktane\Tax\Events;

use Quicktane\Core\Events\OperationContext;

class AfterTaxCalculate
{
    public function __construct(
        public readonly OperationContext $context,
    ) {}
}
