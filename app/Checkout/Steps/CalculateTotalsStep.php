<?php

declare(strict_types=1);

namespace App\Checkout\Steps;

use Closure;
use Quicktane\Core\Pipeline\Pipeline;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;

class CalculateTotalsStep implements PipelineStep
{
    public function __construct(
        private readonly Pipeline $pipeline,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $totalsContext = new PipelineContext;

        foreach ($context->all() as $key => $value) {
            $totalsContext->set($key, $value);
        }

        $result = $this->pipeline->run('checkout.totals', $totalsContext);

        foreach ($result->data as $key => $value) {
            $context->set($key, $value);
        }

        return $next($context);
    }

    public function compensate(PipelineContext $context): void {}

    public static function priority(): int
    {
        return 900;
    }

    public static function pipeline(): string
    {
        return 'checkout.place';
    }
}
