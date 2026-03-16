<?php

declare(strict_types=1);

namespace Quicktane\CMS\Events;

use Quicktane\CMS\Models\Block;
use Quicktane\Core\Events\OperationContext;

class AfterBlockUpdate
{
    public function __construct(
        public readonly Block $block,
        public readonly OperationContext $context,
    ) {}
}
