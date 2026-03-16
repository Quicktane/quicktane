<?php

declare(strict_types=1);

namespace Quicktane\Core\Events;

use Illuminate\Contracts\Events\Dispatcher;
use Quicktane\Core\Trace\OperationTracer;
use Quicktane\Core\Trace\TraceEntry;

class EventDispatcher
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly OperationTracer $operationTracer,
    ) {}

    public function dispatch(object $event): void
    {
        $startTime = microtime(true);

        $listeners = $this->dispatcher->getListeners($event::class);

        usort($listeners, function (mixed $listenerA, mixed $listenerB): int {
            $priorityA = $this->getListenerPriority($listenerA);
            $priorityB = $this->getListenerPriority($listenerB);

            return $priorityB <=> $priorityA;
        });

        foreach ($listeners as $listener) {
            $listener($event::class, [$event]);
        }

        $durationMs = (microtime(true) - $startTime) * 1000;

        $this->operationTracer->record(new TraceEntry(
            traceId: $this->operationTracer->currentTraceId(),
            operation: $event::class,
            type: 'event',
            class: $event::class,
            durationMs: $durationMs,
            metadata: [],
            status: 'completed',
        ));
    }

    private function getListenerPriority(mixed $listener): int
    {
        if (is_string($listener) && property_exists($listener, 'priority')) {
            return $listener::$priority;
        }

        if (is_array($listener) && is_object($listener[0]) && property_exists($listener[0], 'priority')) {
            return $listener[0]::$priority;
        }

        return 0;
    }
}
