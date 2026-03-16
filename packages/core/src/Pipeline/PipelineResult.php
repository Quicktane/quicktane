<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

readonly class PipelineResult
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public bool $isSuspended,
        public ?string $token = null,
        public ?string $redirectUrl = null,
        public array $data = [],
    ) {}

    public static function completed(PipelineContext $context): self
    {
        return new self(
            isSuspended: false,
            data: $context->toArray(),
        );
    }

    public static function suspended(string $token, string $redirectUrl): self
    {
        return new self(
            isSuspended: true,
            token: $token,
            redirectUrl: $redirectUrl,
        );
    }
}
