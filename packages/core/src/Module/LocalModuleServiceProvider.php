<?php

declare(strict_types=1);

namespace Quicktane\Core\Module;

abstract class LocalModuleServiceProvider extends ModuleServiceProvider
{
    /**
     * Local modules live directly in app/Modules/{Name}/,
     * so the provider file is at the module root (no nested src/).
     */
    public function packagePath(): string
    {
        $reflection = new \ReflectionClass(static::class);

        return dirname((string) $reflection->getFileName());
    }
}
