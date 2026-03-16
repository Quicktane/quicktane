<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Commands;

use Illuminate\Console\Command;
use Quicktane\Core\Module\ModuleInstaller;
use Quicktane\Core\Module\ModuleRegistry;
use Quicktane\Core\Module\ModuleServiceProvider;

class ModuleUninstallCommand extends Command
{
    protected $signature = 'quicktane:module:uninstall {module : Module name to uninstall}
                            {--remove-data : Remove database tables and data}';

    protected $description = 'Uninstall a Quicktane module';

    public function handle(ModuleInstaller $moduleInstaller, ModuleRegistry $moduleRegistry): int
    {
        /** @var string $moduleName */
        $moduleName = $this->argument('module');

        if (! $moduleRegistry->has($moduleName)) {
            $this->error("Module '{$moduleName}' is not registered.");

            return self::FAILURE;
        }

        if (! $moduleInstaller->isInstalled($moduleName)) {
            $this->warn("Module '{$moduleName}' is not installed.");

            return self::SUCCESS;
        }

        $dependents = $moduleInstaller->getDependents($moduleName);

        if ($dependents !== []) {
            $this->error(
                "Cannot uninstall '{$moduleName}' — required by: ".implode(', ', $dependents)
            );

            return self::FAILURE;
        }

        $keepData = ! $this->option('remove-data');

        if (! $keepData && ! $this->confirm("This will remove all data for module '{$moduleName}'. Continue?")) {
            $this->info('Cancelled.');

            return self::SUCCESS;
        }

        $providerClass = $moduleRegistry->get($moduleName);

        if ($providerClass === null) {
            $this->error("Module '{$moduleName}' provider class not found.");

            return self::FAILURE;
        }

        /** @var ModuleServiceProvider|null $provider */
        $provider = app()->getProvider($providerClass);

        if ($provider === null) {
            $this->error("Module '{$moduleName}' provider could not be resolved.");

            return self::FAILURE;
        }

        $moduleInstaller->uninstall($provider, $keepData);

        $this->info("Module '{$moduleName}' uninstalled".($keepData ? ' (data preserved).' : ' (data removed).'));

        return self::SUCCESS;
    }
}
