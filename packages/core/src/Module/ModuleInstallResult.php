<?php

declare(strict_types=1);

namespace Quicktane\Core\Module;

readonly class ModuleInstallResult
{
    /**
     * @param  array<string>  $executedUpgrades
     */
    public function __construct(
        public string $module,
        public string $action,
        public ?string $fromVersion,
        public string $toVersion,
        public array $executedUpgrades,
    ) {}
}
