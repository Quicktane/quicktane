<?php

declare(strict_types=1);

namespace Quicktane\Core\Module;

use Illuminate\Support\ServiceProvider;
use Quicktane\Core\Module\Config\ConfigField;
use Quicktane\Core\Module\Menu\MenuItem;

abstract class ModuleServiceProvider extends ServiceProvider
{
    abstract public function moduleName(): string;

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * @return array<string>
     */
    public function dependencies(): array
    {
        return [];
    }

    /**
     * @return array<ConfigField>
     */
    public function configSchema(): array
    {
        return [];
    }

    /**
     * @return array<MenuItem>
     */
    public function adminMenu(): array
    {
        return [];
    }

    /**
     * @return array<class-string, string>
     */
    public function scheduledTasks(): array
    {
        return [];
    }

    public function demoSeeder(): ?string
    {
        return null;
    }

    public function install(): void {}

    protected function runAllUpgrades(): void
    {
        /** @var ModuleInstaller $moduleInstaller */
        $moduleInstaller = $this->app->make(ModuleInstaller::class);
        $moduleInstaller->runUpgrades($this, '0.0.0', $this->version());
    }

    public function uninstall(bool $keepData = true): void {}

    /**
     * @return array<class-string<ModuleUpgrade>>
     */
    public function upgrades(): array
    {
        return [];
    }

    public function packagePath(): string
    {
        $reflection = new \ReflectionClass(static::class);

        return dirname((string) $reflection->getFileName(), 2);
    }

    protected function loadModuleRoutes(): void
    {
        $routesPath = $this->packagePath().'/routes';

        if (file_exists($routesPath.'/api.php')) {
            $this->loadRoutesFrom($routesPath.'/api.php');
        }

        if (file_exists($routesPath.'/admin.php')) {
            $this->loadRoutesFrom($routesPath.'/admin.php');
        }
    }

    protected function loadModuleMigrations(): void
    {
        $basePath = $this->packagePath();

        // Support both lowercase (packages) and uppercase (app modules) directory names
        // to ensure compatibility across case-sensitive filesystems (Linux/K8s).
        foreach (['database/migrations', 'Database/migrations'] as $path) {
            $migrationsPath = $basePath.'/'.$path;

            if (is_dir($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);

                return;
            }
        }
    }

    protected function loadModuleConfig(): void
    {
        $configPath = $this->packagePath().'/config';

        if (is_dir($configPath)) {
            foreach (glob($configPath.'/*.php') as $configFile) {
                $key = $this->moduleName().'.'.basename($configFile, '.php');
                $this->mergeConfigFrom($configFile, $key);
            }
        }
    }
}
