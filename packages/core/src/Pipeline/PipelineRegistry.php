<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

class PipelineRegistry
{
    /** @var array<string, list<class-string<PipelineStep>>> */
    private array $steps = [];

    /** @var array<string, array<class-string<PipelineStep>, class-string<PipelineStep>>> */
    private array $replacements = [];

    /**
     * @param  class-string<PipelineStep>  $stepClass
     */
    public function register(string $pipeline, string $stepClass): void
    {
        $this->steps[$pipeline][] = $stepClass;
    }

    /**
     * @param  class-string<PipelineStep>  $originalStep
     * @param  class-string<PipelineStep>  $replacementStep
     */
    public function replace(string $pipeline, string $originalStep, string $replacementStep): void
    {
        $this->replacements[$pipeline][$originalStep] = $replacementStep;
    }

    /**
     * @return list<class-string<PipelineStep>>
     */
    public function getSteps(string $pipeline): array
    {
        $steps = $this->steps[$pipeline] ?? [];

        /** @var array<class-string<PipelineStep>, class-string<PipelineStep>> $configReplacements */
        $configReplacements = config("modules.pipeline_replacements.{$pipeline}", []);
        $allReplacements = array_merge(
            $this->replacements[$pipeline] ?? [],
            $configReplacements,
        );

        $steps = array_map(
            fn (string $step): string => $allReplacements[$step] ?? $step,
            $steps,
        );

        usort($steps, fn (string $stepA, string $stepB): int => $stepB::priority() <=> $stepA::priority());

        return array_values($steps);
    }
}
