<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline\Events;

use Quicktane\Core\Pipeline\SuspendedPipeline;

readonly class PipelineForceCompleted
{
    public function __construct(
        public SuspendedPipeline $suspendedPipeline,
    ) {}
}
