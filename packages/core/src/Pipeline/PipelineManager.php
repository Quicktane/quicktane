<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Quicktane\Core\Pipeline\Events\PipelineForceCompleted;

class PipelineManager
{
    public function __construct(
        private readonly Container $container,
        private readonly Dispatcher $eventDispatcher,
    ) {}

    public function forceCompleteAll(): ForceCompleteResult
    {
        $completed = [];
        $blocked = [];

        /** @var list<string> $nonCompletableSteps */
        $nonCompletableSteps = config('pipelines.non_completable_steps', []);

        $suspendedPipelines = SuspendedPipeline::suspended()->get();

        foreach ($suspendedPipelines as $suspendedPipeline) {
            if (in_array($suspendedPipeline->current_step, $nonCompletableSteps, true)) {
                $blocked[] = $suspendedPipeline->token;

                continue;
            }

            $context = new PipelineContext;

            foreach ($suspendedPipeline->context as $key => $value) {
                $context->set($key, $value);
            }

            $this->compensateCompletedSteps(
                $suspendedPipeline->completed_steps ?? [],
                $context,
            );

            $suspendedPipeline->update(['status' => 'force_completed']);

            $this->eventDispatcher->dispatch(new PipelineForceCompleted($suspendedPipeline));

            $completed[] = $suspendedPipeline->token;
        }

        return new ForceCompleteResult($completed, $blocked);
    }

    public function getActivePipelineCount(): int
    {
        return SuspendedPipeline::suspended()->count();
    }

    /**
     * @return Collection<int, SuspendedPipeline>
     */
    public function getSuspendedPipelines(): Collection
    {
        return SuspendedPipeline::suspended()->get();
    }

    /**
     * @param  list<string>  $completedStepClasses
     */
    private function compensateCompletedSteps(array $completedStepClasses, PipelineContext $context): void
    {
        foreach (array_reverse($completedStepClasses) as $stepClass) {
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
