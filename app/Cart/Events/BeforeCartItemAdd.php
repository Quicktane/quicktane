<?php

declare(strict_types=1);

namespace App\Cart\Events;

use Quicktane\Core\Events\OperationContext;

class BeforeCartItemAdd
{
    public function __construct(
        public readonly OperationContext $context,
    ) {}
}
