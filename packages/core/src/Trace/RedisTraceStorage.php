<?php

declare(strict_types=1);

namespace Quicktane\Core\Trace;

use DateTimeImmutable;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

class RedisTraceStorage implements TraceStorage
{
    private const int TTL_SECONDS = 3600;

    public function store(TraceEntry $entry): void
    {
        $key = $this->key($entry->traceId ?? 'unknown');

        $this->connection()->rpush($key, [json_encode($entry->toArray(), JSON_THROW_ON_ERROR)]);
        $this->connection()->expire($key, self::TTL_SECONDS);
    }

    /**
     * @return list<TraceEntry>
     */
    public function flush(string $traceId): array
    {
        $key = $this->key($traceId);
        /** @var list<string> $items */
        $items = $this->connection()->lrange($key, 0, -1);
        $this->connection()->del($key);

        $entries = [];

        foreach ($items as $item) {
            if (! is_string($item)) {
                continue;
            }

            try {
                $entries[] = $this->deserialize($item);
            } catch (\JsonException) {
                continue;
            }
        }

        return $entries;
    }

    public function delete(string $traceId): void
    {
        $this->connection()->del($this->key($traceId));
    }

    private function key(string $traceId): string
    {
        return "trace:{$traceId}";
    }

    private function connection(): Connection
    {
        return Redis::connection();
    }

    private function deserialize(string $json): TraceEntry
    {
        /** @var array<string, mixed> $data */
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return new TraceEntry(
            traceId: (string) $data['trace_id'],
            operation: (string) $data['operation'],
            type: (string) $data['type'],
            class: (string) $data['class'],
            durationMs: (float) $data['duration_ms'],
            metadata: (array) $data['metadata'],
            status: (string) $data['status'],
            createdAt: new DateTimeImmutable((string) $data['created_at']),
        );
    }
}
