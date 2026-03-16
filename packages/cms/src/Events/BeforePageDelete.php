<?php

declare(strict_types=1);

namespace Quicktane\CMS\Events;

use Quicktane\CMS\Models\Page;
use Quicktane\Core\Events\OperationContext;

class BeforePageDelete
{
    public function __construct(
        public readonly Page $page,
        public readonly OperationContext $context,
    ) {}
}
