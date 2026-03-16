<?php

declare(strict_types=1);

namespace Quicktane\Core\Module;

use Illuminate\Contracts\Foundation\Application;

class ModuleReplacer
{
    public function __construct(
        private readonly Application $application,
    ) {}

    public function applyReplacements(): void
    {
        /** @var array<class-string, class-string> $replacements */
        $replacements = config('modules.replacements', []);

        foreach ($replacements as $abstract => $concrete) {
            $this->application->bind($abstract, $concrete);
        }
    }
}
