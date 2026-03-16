<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

use RuntimeException;

class PipelineSuspendException extends RuntimeException
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly string $redirectUrl,
        public readonly string $reason,
        public readonly array $metadata = [],
    ) {
        parent::__construct("Pipeline suspended: {$reason}");
    }
}
