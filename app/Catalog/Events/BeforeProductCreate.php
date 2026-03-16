<?php

declare(strict_types=1);

namespace App\Catalog\Events;

use Quicktane\Core\Events\OperationContext;

class BeforeProductCreate
{
    public function __construct(
        public readonly OperationContext $context,
    ) {}
}
