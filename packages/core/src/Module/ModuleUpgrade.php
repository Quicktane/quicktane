<?php

declare(strict_types=1);

namespace Quicktane\Core\Module;

interface ModuleUpgrade
{
    public function version(): string;

    public function run(): void;
}
