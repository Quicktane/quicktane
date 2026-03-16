<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Quicktane\Core\Module\ModuleInstaller;
use Quicktane\Core\Module\ModuleRegistry;
use Quicktane\Core\Module\ModuleServiceProvider;

class ModuleInstallCommand extends Command
{
    protected $signature = 'quicktane:module:install {module? : Module name to install (omit for all)}
                            {--with-demo-data : Seed demo data after installation}';

    protected $description = 'Install or upgrade Quicktane modules';

    public function handle(ModuleInstaller $moduleInstaller, ModuleRegistry $moduleRegistry): int
    {
        /** @var string|null $moduleName */
        $moduleName = $this->argument('module');

        $this->info('Running pending migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->line(trim(Artisan::output()));
        $this->newLine();

        if ($moduleName !== null) {
            return $this->setupModule($moduleInstaller, $moduleRegistry, $moduleName);
        }

        return $this->setupAllModules($moduleInstaller, $moduleRegistry);
    }

    private function setupModule(
        ModuleInstaller $moduleInstaller,
        ModuleRegistry $moduleRegistry,
        string $moduleName,
    ): int {
        if (! $moduleRegistry->has($moduleName)) {
            $this->error("Module '{$moduleName}' is not registered. Check config/modules.php.");

            return self::FAILURE;
        }

        $provider = $this->resolveProvider($moduleRegistry, $moduleName);

        if ($provider === null) {
            $this->error("Module '{$moduleName}' provider could not be resolved.");

            return self::FAILURE;
        }

        $missingDependencies = $moduleInstaller->checkDependencies($provider);

        if ($missingDependencies !== []) {
            $this->error(
                "Module '{$moduleName}' requires missing modules: ".implode(', ', $missingDependencies)
            );

            return self::FAILURE;
        }

        $result = $moduleInstaller->setup($provider);

        match ($result->action) {
            'installed' => $this->info("Module '{$result->module}' installed (v{$result->toVersion})."),
            'upgraded' => $this->info(
                "Module '{$result->module}' upgraded from v{$result->fromVersion} to v{$result->toVersion}."
                .($result->executedUpgrades !== []
                    ? ' Upgrades: '.implode(', ', $result->executedUpgrades)
                    : '')
            ),
            'up-to-date' => $this->line("Module '{$result->module}' is already up to date (v{$result->toVersion})."),
            default => null,
        };

        if ($this->option('with-demo-data') && $result->action === 'installed') {
            $this->seedDemoData($provider);
        }

        return self::SUCCESS;
    }

    private function setupAllModules(ModuleInstaller $moduleInstaller, ModuleRegistry $moduleRegistry): int
    {
        $modules = $moduleRegistry->all();

        if ($modules === []) {
            $this->warn('No modules registered.');

            return self::SUCCESS;
        }

        $this->info('Setting up '.count($modules).' module(s)...');
        $this->newLine();

        foreach ($modules as $name => $providerClass) {
            $provider = $this->resolveProvider($moduleRegistry, $name);

            if ($provider === null) {
                $this->warn("  Skipping '{$name}' — provider not resolved.");

                continue;
            }

            $missingDependencies = $moduleInstaller->checkDependencies($provider);

            if ($missingDependencies !== []) {
                $this->warn("  Skipping '{$name}' — missing deps: ".implode(', ', $missingDependencies));

                continue;
            }

            $result = $moduleInstaller->setup($provider);

            match ($result->action) {
                'installed' => $this->info("  {$result->module}: installed v{$result->toVersion}"),
                'upgraded' => $this->info(
                    "  {$result->module}: upgraded v{$result->fromVersion} → v{$result->toVersion}"
                    .($result->executedUpgrades !== []
                        ? ' ['.implode(', ', $result->executedUpgrades).']'
                        : '')
                ),
                'up-to-date' => $this->line("  {$result->module}: up to date (v{$result->toVersion})"),
                default => null,
            };

            if ($this->option('with-demo-data') && $result->action === 'installed') {
                $this->seedDemoData($provider);
            }
        }

        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }

    private function resolveProvider(ModuleRegistry $moduleRegistry, string $moduleName): ?ModuleServiceProvider
    {
        $providerClass = $moduleRegistry->get($moduleName);

        if ($providerClass === null) {
            return null;
        }

        /** @var ModuleServiceProvider|null $provider */
        $provider = app()->getProvider($providerClass);

        return $provider;
    }

    private function seedDemoData(ModuleServiceProvider $provider): void
    {
        $seederClass = $provider->demoSeeder();

        if ($seederClass === null) {
            return;
        }

        Artisan::call('db:seed', ['--class' => $seederClass, '--force' => true]);
        $this->line("  Demo data seeded for '{$provider->moduleName()}'.");
    }
}
