<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Quicktane\Core\Module\Models\InstalledModule;
use Quicktane\Core\Module\ModuleRegistry;
use Quicktane\Core\Module\ModuleServiceProvider;

class ModuleStatusCommand extends Command
{
    protected $signature = 'quicktane:module:status';

    protected $description = 'Show status of all registered Quicktane modules';

    public function handle(ModuleRegistry $moduleRegistry): int
    {
        $modules = $moduleRegistry->all();

        if ($modules === []) {
            $this->warn('No modules registered.');

            return self::SUCCESS;
        }

        $installedModules = Schema::hasTable('module_versions')
            ? InstalledModule::all()->keyBy('name')
            : collect();

        $rows = [];

        foreach ($modules as $name => $providerClass) {
            /** @var ModuleServiceProvider|null $provider */
            $provider = app()->getProvider($providerClass);

            $codeVersion = $provider?->version() ?? '?';
            $installed = $installedModules->get($name);

            if ($installed === null) {
                $status = 'Not installed';
                $installedVersion = '—';
            } elseif (version_compare($installed->version, $codeVersion, '<')) {
                $status = 'Upgrade available';
                $installedVersion = $installed->version;
            } else {
                $status = 'Installed';
                $installedVersion = $installed->version;
            }

            $rows[] = [
                $name,
                $installedVersion,
                $codeVersion,
                $status,
            ];
        }

        $this->table(
            ['Module', 'Installed', 'Available', 'Status'],
            $rows,
        );

        return self::SUCCESS;
    }
}
