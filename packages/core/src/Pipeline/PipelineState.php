<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

use DateTimeImmutable;

readonly class PipelineState
{
    /**
     * @param  list<string>  $completedSteps
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $token,
        public string $pipelineName,
        public array $completedSteps,
        public int $currentStepIndex,
        public PipelineContext $context,
        public array $metadata,
        public string $reason,
        public DateTimeImmutable $expiresAt,
    ) {}
}
