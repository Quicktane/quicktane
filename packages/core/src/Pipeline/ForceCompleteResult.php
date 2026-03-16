<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

readonly class ForceCompleteResult
{
    /**
     * @param  list<string>  $completed
     * @param  list<string>  $blocked
     */
    public function __construct(
        public array $completed,
        public array $blocked,
    ) {}
}
