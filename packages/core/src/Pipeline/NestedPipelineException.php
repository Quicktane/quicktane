<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

use RuntimeException;

class NestedPipelineException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Nested pipeline execution is not allowed');
    }
}
