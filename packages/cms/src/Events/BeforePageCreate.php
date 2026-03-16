<?php

declare(strict_types=1);

namespace Quicktane\CMS\Events;

use Quicktane\Core\Events\OperationContext;

class BeforePageCreate
{
    public function __construct(
        public readonly OperationContext $context,
    ) {}
}
