<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

use DateTimeImmutable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Quicktane\Core\Trace\OperationTracer;
use Quicktane\Core\Trace\TraceEntry;

class Pipeline
{
    private static int $executionDepth = 0;

    private const int MAX_DEPTH = 5;

    public function __construct(
        private readonly PipelineRegistry $pipelineRegistry,
        private readonly PipelineStateStorage $pipelineStateStorage,
        private readonly OperationTracer $operationTracer,
        private readonly Container $container,
    ) {}

    public function run(string $name, PipelineContext $context): PipelineResult
    {
        if (self::$executionDepth >= self::MAX_DEPTH) {
            throw new NestedPipelineException;
        }

        self::$executionDepth++;

        try {
            $steps = $this->pipelineRegistry->getSteps($name);

            return $this->executeSteps($name, $steps, $context, 0, []);
        } finally {
            self::$executionDepth--;
        }
    }

    /**
     * @param  array<string, mixed>  $callbackData
     */
    public function resume(string $token, array $callbackData = []): PipelineResult
    {
        if (self::$executionDepth >= self::MAX_DEPTH) {
            throw new NestedPipelineException;
        }

        self::$executionDepth++;

        try {
            /** @var SuspendedPipeline|null $suspendedPipeline */
            $suspendedPipeline = DB::transaction(function () use ($token): ?SuspendedPipeline {
                return SuspendedPipeline::where('token', $token)
                    ->where('status', 'suspended')
                    ->lockForUpdate()
                    ->first();
            });

            if ($suspendedPipeline === null) {
                throw new \RuntimeException("No suspended pipeline found for token: {$token}");
            }

            $state = $this->pipelineStateStorage->load($token);

            if ($state === null) {
                throw new \RuntimeException("Pipeline state expired for token: {$token}");
            }

            $context = $state->context;

            foreach ($callbackData as $key => $value) {
                $context->set($key, $value);
            }

            $steps = $this->pipelineRegistry->getSteps($state->pipelineName);
            $resumeIndex = $state->currentStepIndex;

            $suspendedPipeline->update(['status' => 'resuming']);
            $this->pipelineStateStorage->delete($token);

            $result = $this->executeSteps(
                $state->pipelineName,
                $steps,
                $context,
                $resumeIndex,
                $state->completedSteps,
            );

            if (! $result->isSuspended) {
                $suspendedPipeline->update([
                    'status' => 'completed',
                    'result' => $result->data,
                ]);
            }

            return $result;
        } finally {
            self::$executionDepth--;
        }
    }

    /**
     * @param  list<class-string<PipelineStep>>  $steps
     * @param  list<string>  $completedSteps
     */
    private function executeSteps(
        string $name,
        array $steps,
        PipelineContext $context,
        int $startIndex,
        array $completedSteps,
    ): PipelineResult {
        for ($index = $startIndex; $index < count($steps); $index++) {
            $stepClass = $steps[$index];

            /** @var PipelineStep $step */
            $step = $this->container->make($stepClass);

            $startTime = microtime(true);

            try {
                $step->handle($context, function () {});

                $durationMs = (microtime(true) - $startTime) * 1000;

                $this->operationTracer->record(new TraceEntry(
                    traceId: $this->operationTracer->currentTraceId(),
                    operation: "pipeline.{$name}.step",
                    type: 'pipeline_step',
                    class: $stepClass,
                    durationMs: $durationMs,
                    metadata: ['step_index' => $index],
                    status: 'completed',
                ));

                $completedSteps[] = $stepClass;
            } catch (PipelineSuspendException $exception) {
                $token = Str::uuid()->toString();
                $ttl = (int) config("pipelines.ttl.{$name}", config('pipelines.ttl.default', 3600));

                $state = new PipelineState(
                    token: $token,
                    pipelineName: $name,
                    completedSteps: $completedSteps,
                    currentStepIndex: $index,
                    context: $context,
                    metadata: $exception->metadata,
                    reason: $exception->reason,
                    expiresAt: new DateTimeImmutable("+{$ttl} seconds"),
                );

                $this->pipelineStateStorage->save($state);

                SuspendedPipeline::create([
                    'token' => $token,
                    'pipeline_name' => $name,
                    'status' => 'suspended',
                    'context' => $context->toArray(),
                    'completed_steps' => $completedSteps,
                    'current_step' => $stepClass,
                    'reason' => $exception->reason,
                    'metadata' => $exception->metadata,
                    'expires_at' => $state->expiresAt->format('Y-m-d H:i:s'),
                ]);

                return PipelineResult::suspended($token, $exception->redirectUrl);
            } catch (\Throwable $exception) {
                $this->compensateSteps($steps, $completedSteps, $context);

                throw $exception;
            }
        }

        return PipelineResult::completed($context);
    }

    /**
     * @param  list<class-string<PipelineStep>>  $allSteps
     * @param  list<string>  $completedSteps
     */
    private function compensateSteps(array $allSteps, array $completedSteps, PipelineContext $context): void
    {
        foreach (array_reverse($completedSteps) as $stepClass) {
            /** @var PipelineStep $step */
            $step = $this->container->make($stepClass);

            try {
                $step->compensate($context);
            } catch (\Throwable) {
                // Log but don't re-throw during compensation
            }
        }
    }
}
