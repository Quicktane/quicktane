<?php

declare(strict_types=1);

namespace Quicktane\Core\Trace;

use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class DatabaseTraceStorage implements TraceStorage
{
    public function store(TraceEntry $entry): void
    {
        DB::table('operation_traces')->insert([
            'trace_id' => $entry->traceId,
            'operation' => $entry->operation,
            'type' => $entry->type,
            'class' => $entry->class,
            'duration_ms' => $entry->durationMs,
            'metadata' => json_encode($entry->metadata, JSON_THROW_ON_ERROR),
            'status' => $entry->status,
            'created_at' => $entry->createdAt->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return list<TraceEntry>
     */
    public function flush(string $traceId): array
    {
        $rows = DB::table('operation_traces')
            ->where('trace_id', $traceId)
            ->get();

        DB::table('operation_traces')
            ->where('trace_id', $traceId)
            ->delete();

        return $rows->map(fn (object $row): TraceEntry => new TraceEntry(
            traceId: $row->trace_id,
            operation: $row->operation,
            type: $row->type,
            class: $row->class,
            durationMs: (float) $row->duration_ms,
            metadata: json_decode($row->metadata, true, 512, JSON_THROW_ON_ERROR),
            status: $row->status,
            createdAt: new DateTimeImmutable($row->created_at),
        ))->all();
    }

    public function delete(string $traceId): void
    {
        DB::table('operation_traces')
            ->where('trace_id', $traceId)
            ->delete();
    }
}
