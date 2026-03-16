<?php

declare(strict_types=1);

namespace Quicktane\Core\Module;

class ModuleRegistry
{
    /** @var array<string, class-string<ModuleServiceProvider>> */
    private array $modules = [];

    /**
     * @param  class-string<ModuleServiceProvider>  $providerClass
     */
    public function register(string $name, string $providerClass): void
    {
        $this->modules[$name] = $providerClass;
    }

    public function has(string $name): bool
    {
        return isset($this->modules[$name]);
    }

    /**
     * @return class-string<ModuleServiceProvider>|null
     */
    public function get(string $name): ?string
    {
        return $this->modules[$name] ?? null;
    }

    /**
     * @return array<string, class-string<ModuleServiceProvider>>
     */
    public function all(): array
    {
        return $this->modules;
    }
}
