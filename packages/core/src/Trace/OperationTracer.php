<?php

declare(strict_types=1);

namespace Quicktane\Core\Trace;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OperationTracer
{
    private ?string $traceId = null;

    public function __construct(
        private readonly RedisTraceStorage $redisTraceStorage,
        private readonly DatabaseTraceStorage $databaseTraceStorage,
    ) {}

    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public function execute(string $operation, callable $callback): mixed
    {
        $this->traceId = Str::uuid()->toString();
        $startTime = microtime(true);

        try {
            $result = DB::transaction($callback);

            $durationMs = (microtime(true) - $startTime) * 1000;

            $this->record(new TraceEntry(
                traceId: $this->traceId,
                operation: $operation,
                type: 'operation',
                class: $operation,
                durationMs: $durationMs,
                metadata: [],
                status: 'completed',
            ));

            $this->flushToDatabase();

            return $result;
        } catch (\Throwable $exception) {
            $durationMs = (microtime(true) - $startTime) * 1000;

            $this->record(new TraceEntry(
                traceId: $this->traceId,
                operation: $operation,
                type: 'operation',
                class: $operation,
                durationMs: $durationMs,
                metadata: ['error' => $exception->getMessage()],
                status: 'failed',
            ));

            $this->flushToDatabase();

            throw $exception;
        } finally {
            $this->traceId = null;
        }
    }

    public function record(TraceEntry $entry): void
    {
        if ($this->traceId === null) {
            return;
        }

        $this->redisTraceStorage->store($entry);
    }

    public function currentTraceId(): ?string
    {
        return $this->traceId;
    }

    private function flushToDatabase(): void
    {
        if ($this->traceId === null) {
            return;
        }

        $entries = $this->redisTraceStorage->flush($this->traceId);

        foreach ($entries as $entry) {
            $this->databaseTraceStorage->store($entry);
        }
    }
}
