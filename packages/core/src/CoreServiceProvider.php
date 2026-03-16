<?php

declare(strict_types=1);

namespace Quicktane\Core;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Quicktane\Core\Module\Commands\ModuleInstallCommand;
use Quicktane\Core\Module\Commands\ModuleStatusCommand;
use Quicktane\Core\Module\Commands\ModuleUninstallCommand;
use Quicktane\Core\Module\Menu\MenuRegistry;
use Quicktane\Core\Module\ModuleRegistry;
use Quicktane\Core\Module\ModuleReplacer;
use Quicktane\Core\Module\ModuleServiceProvider;
use Quicktane\Core\Pipeline\Commands\PipelineCleanupCommand;
use Quicktane\Core\Pipeline\Pipeline;
use Quicktane\Core\Pipeline\PipelineRegistry;
use Quicktane\Core\Pipeline\PipelineStateStorage;
use Quicktane\Core\Pipeline\RedisPipelineStateStorage;
use Quicktane\Core\Trace\DatabaseTraceStorage;
use Quicktane\Core\Trace\OperationTracer;
use Quicktane\Core\Trace\RedisTraceStorage;
use Quicktane\Core\Trace\TraceStorage;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleRegistry::class);
        $this->app->singleton(MenuRegistry::class);

        $this->registerModules();

        $this->app->singleton(RedisTraceStorage::class);
        $this->app->singleton(DatabaseTraceStorage::class);
        $this->app->bind(TraceStorage::class, RedisTraceStorage::class);
        $this->app->scoped(OperationTracer::class);

        $this->app->singleton(PipelineRegistry::class);
        $this->app->scoped(Pipeline::class);
        $this->app->singleton(PipelineStateStorage::class, RedisPipelineStateStorage::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $replacer = new ModuleReplacer($this->app);
        $replacer->applyReplacements();

        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');

        $this->registerModuleMenuItems();
        $this->registerModuleScheduledTasks();

        if ($this->app->runningInConsole()) {
            $this->commands([
                PipelineCleanupCommand::class,
                ModuleInstallCommand::class,
                ModuleUninstallCommand::class,
                ModuleStatusCommand::class,
            ]);
        }
    }

    private function registerModules(): void
    {
        /** @var array<class-string<ModuleServiceProvider>> $modules */
        $modules = config('modules.modules', []);

        /** @var ModuleRegistry $registry */
        $registry = $this->app->make(ModuleRegistry::class);

        foreach ($modules as $providerClass) {
            $this->registerModule($registry, $providerClass);
        }

        $this->discoverLocalModules($registry);
    }

    /**
     * @param  class-string<ModuleServiceProvider>  $providerClass
     */
    private function registerModule(ModuleRegistry $registry, string $providerClass): void
    {
        $this->app->register($providerClass);

        /** @var ModuleServiceProvider|null $provider */
        $provider = $this->app->getProvider($providerClass);

        if ($provider !== null) {
            $registry->register($provider->moduleName(), $providerClass);
        }
    }

    private function discoverLocalModules(ModuleRegistry $registry): void
    {
        /** @var string|null $localPath */
        $localPath = config('modules.local_path');

        if ($localPath === null || ! is_dir($localPath)) {
            return;
        }

        $directories = glob($localPath.'/*', GLOB_ONLYDIR);

        if ($directories === false) {
            return;
        }

        foreach ($directories as $directory) {
            $moduleName = basename($directory);
            $providerFile = $directory.'/'.$moduleName.'ServiceProvider.php';

            if (! file_exists($providerFile)) {
                continue;
            }

            require_once $providerFile;

            $providerClass = "App\\{$moduleName}\\{$moduleName}ServiceProvider";

            if (! class_exists($providerClass, false)) {
                continue;
            }

            if (! is_subclass_of($providerClass, ModuleServiceProvider::class)) {
                continue;
            }

            $this->registerModule($registry, $providerClass);
        }
    }

    private function registerModuleMenuItems(): void
    {
        /** @var MenuRegistry $menuRegistry */
        $menuRegistry = $this->app->make(MenuRegistry::class);

        /** @var ModuleRegistry $moduleRegistry */
        $moduleRegistry = $this->app->make(ModuleRegistry::class);

        foreach ($moduleRegistry->all() as $providerClass) {
            /** @var ModuleServiceProvider|null $provider */
            $provider = $this->app->getProvider($providerClass);

            if ($provider === null) {
                continue;
            }

            $menuRegistry->registerMany($provider->adminMenu());
        }
    }

    private function registerModuleScheduledTasks(): void
    {
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule): void {
            /** @var ModuleRegistry $moduleRegistry */
            $moduleRegistry = $this->app->make(ModuleRegistry::class);

            foreach ($moduleRegistry->all() as $providerClass) {
                /** @var ModuleServiceProvider|null $provider */
                $provider = $this->app->getProvider($providerClass);

                if ($provider === null) {
                    continue;
                }

                foreach ($provider->scheduledTasks() as $taskClass => $cronExpression) {
                    $schedule->job($this->app->make($taskClass))->cron($cronExpression);
                }
            }
        });
    }
}
