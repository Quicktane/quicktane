<?php

declare(strict_types=1);

namespace Quicktane\Core\Events;

use RuntimeException;

class OperationBlockedException extends RuntimeException
{
    public function __construct(
        public readonly string $operation,
        public readonly string $reason,
        public readonly string $blocker,
    ) {
        parent::__construct("Operation [{$operation}] blocked by [{$blocker}]: {$reason}");
    }
}
