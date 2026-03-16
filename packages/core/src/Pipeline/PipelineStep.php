<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

use Closure;

interface PipelineStep
{
    public function handle(PipelineContext $context, Closure $next): mixed;

    public function compensate(PipelineContext $context): void;

    public static function priority(): int;

    public static function pipeline(): string;
}
