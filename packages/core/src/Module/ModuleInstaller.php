<?php

declare(strict_types=1);

namespace Quicktane\Core\Module;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Quicktane\Core\Module\Models\InstalledModule;

class ModuleInstaller
{
    public function __construct(
        private readonly Container $container,
        private readonly ModuleRegistry $moduleRegistry,
    ) {}

    public function setup(ModuleServiceProvider $provider): ModuleInstallResult
    {
        $moduleName = $provider->moduleName();
        $targetVersion = $provider->version();
        $installedModule = InstalledModule::where('name', $moduleName)->first();

        if ($installedModule === null) {
            return $this->freshInstall($provider, $targetVersion);
        }

        return $this->upgradeModule($provider, $installedModule, $targetVersion);
    }

    public function uninstall(ModuleServiceProvider $provider, bool $keepData = true): void
    {
        $provider->uninstall($keepData);

        if (! $keepData) {
            $this->rollbackMigrations($provider);
        }

        InstalledModule::where('name', $provider->moduleName())->delete();
    }

    /**
     * @return array<string>
     */
    public function checkDependencies(ModuleServiceProvider $provider): array
    {
        $missingDependencies = [];

        foreach ($provider->dependencies() as $dependency) {
            if (! $this->moduleRegistry->has($dependency)) {
                $missingDependencies[] = $dependency;
            }
        }

        return $missingDependencies;
    }

    /**
     * @return array<string>
     */
    public function getDependents(string $moduleName): array
    {
        $dependents = [];

        foreach ($this->moduleRegistry->all() as $name => $providerClass) {
            /** @var ModuleServiceProvider|null $provider */
            $provider = $this->container->make($providerClass);

            if ($provider !== null && in_array($moduleName, $provider->dependencies(), true)) {
                $dependents[] = $name;
            }
        }

        return $dependents;
    }

    public function isInstalled(string $moduleName): bool
    {
        if (! Schema::hasTable('module_versions')) {
            return false;
        }

        return InstalledModule::where('name', $moduleName)->exists();
    }

    public function getInstalledVersion(string $moduleName): ?string
    {
        if (! Schema::hasTable('module_versions')) {
            return null;
        }

        return InstalledModule::where('name', $moduleName)->value('version');
    }

    private function freshInstall(ModuleServiceProvider $provider, string $targetVersion): ModuleInstallResult
    {
        $provider->install();

        InstalledModule::create([
            'name' => $provider->moduleName(),
            'version' => $targetVersion,
            'installed_at' => now(),
        ]);

        return new ModuleInstallResult(
            module: $provider->moduleName(),
            action: 'installed',
            fromVersion: null,
            toVersion: $targetVersion,
            executedUpgrades: [],
        );
    }

    private function upgradeModule(
        ModuleServiceProvider $provider,
        InstalledModule $installedModule,
        string $targetVersion,
    ): ModuleInstallResult {
        $installedVersion = $installedModule->version;

        if (version_compare($installedVersion, $targetVersion, '>=')) {
            return new ModuleInstallResult(
                module: $provider->moduleName(),
                action: 'up-to-date',
                fromVersion: $installedVersion,
                toVersion: $targetVersion,
                executedUpgrades: [],
            );
        }

        $executedUpgrades = $this->runUpgrades($provider, $installedVersion, $targetVersion);

        $installedModule->update([
            'version' => $targetVersion,
            'updated_at' => now(),
        ]);

        return new ModuleInstallResult(
            module: $provider->moduleName(),
            action: 'upgraded',
            fromVersion: $installedVersion,
            toVersion: $targetVersion,
            executedUpgrades: $executedUpgrades,
        );
    }

    /**
     * @return array<string>
     */
    public function runUpgrades(ModuleServiceProvider $provider, string $fromVersion, string $toVersion): array
    {
        $upgradeClasses = $provider->upgrades();
        $executedVersions = [];

        $pendingUpgrades = collect($upgradeClasses)
            ->map(fn (string $class): ModuleUpgrade => $this->container->make($class))
            ->filter(fn (ModuleUpgrade $moduleUpgrade): bool => version_compare($moduleUpgrade->version(), $fromVersion, '>') &&
                version_compare($moduleUpgrade->version(), $toVersion, '<=')
            )
            ->sortBy(fn (ModuleUpgrade $moduleUpgrade): string => $moduleUpgrade->version(), SORT_NATURAL);

        foreach ($pendingUpgrades as $moduleUpgrade) {
            $moduleUpgrade->run();
            $executedVersions[] = $moduleUpgrade->version();
        }

        return $executedVersions;
    }

    private function rollbackMigrations(ModuleServiceProvider $provider): void
    {
        $migrationsPath = $provider->packagePath().'/database/migrations';

        if (! is_dir($migrationsPath)) {
            return;
        }

        Artisan::call('migrate:rollback', [
            '--path' => $migrationsPath,
            '--realpath' => true,
            '--force' => true,
        ]);
    }
}
