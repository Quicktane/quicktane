<?php

declare(strict_types=1);

namespace Quicktane\Core\Trace;

interface TraceStorage
{
    public function store(TraceEntry $entry): void;

    /**
     * @return list<TraceEntry>
     */
    public function flush(string $traceId): array;

    public function delete(string $traceId): void;
}
