<?php

declare(strict_types=1);

namespace Quicktane\Core\Pipeline;

interface PipelineStateStorage
{
    public function save(PipelineState $state): void;

    public function load(string $token): ?PipelineState;

    public function delete(string $token): void;
}
