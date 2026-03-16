<?php

declare(strict_types=1);

namespace Quicktane\CMS\Events;

use Quicktane\Core\Events\OperationContext;

class BeforeBlockCreate
{
    public function __construct(
        public readonly OperationContext $context,
    ) {}
}
