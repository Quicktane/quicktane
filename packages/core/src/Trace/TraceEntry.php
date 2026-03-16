<?php

declare(strict_types=1);

namespace Quicktane\Core\Trace;

use DateTimeImmutable;

readonly class TraceEntry
{
    public DateTimeImmutable $createdAt;

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public ?string $traceId,
        public string $operation,
        public string $type,
        public string $class,
        public float $durationMs,
        public array $metadata,
        public string $status,
        ?DateTimeImmutable $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'trace_id' => $this->traceId,
            'operation' => $this->operation,
            'type' => $this->type,
            'class' => $this->class,
            'duration_ms' => $this->durationMs,
            'metadata' => $this->metadata,
            'status' => $this->status,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s.u'),
        ];
    }
}
