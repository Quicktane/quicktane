<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Quicktane\Core\Pipeline\Events\PipelineExpired;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;
use Quicktane\Core\Pipeline\SuspendedPipeline;

class PipelineCleanupCommand extends Command
{
    protected $signature = 'pipeline:cleanup';

    protected $description = 'Clean up expired suspended pipelines';

    public function handle(Container $container, Dispatcher $eventDispatcher): int
    {
        $expiredPipelines = SuspendedPipeline::expired()->get();

        if ($expiredPipelines->isEmpty()) {
            $this->info('No expired pipelines found.');

            return self::SUCCESS;
        }

        foreach ($expiredPipelines as $suspendedPipeline) {
            $context = new PipelineContext;

            foreach ($suspendedPipeline->context as $key => $value) {
                $context->set($key, $value);
            }

            foreach (array_reverse($suspendedPipeline->completed_steps ?? []) as $stepClass) {
                /** @var PipelineStep $step */
                $step = $container->make($stepClass);

                try {
                    $step->compensate($context);
                } catch (\Throwable) {
                    // Log but continue compensating other steps
                }
            }

            $suspendedPipeline->update(['status' => 'expired']);

            $eventDispatcher->dispatch(new PipelineExpired($suspendedPipeline));

            $this->line("Expired pipeline: {$suspendedPipeline->token}");
        }

        $this->info("Cleaned up {$expiredPipelines->count()} expired pipeline(s).");

        return self::SUCCESS;
    }
}
