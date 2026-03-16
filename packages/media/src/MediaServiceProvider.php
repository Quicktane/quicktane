<?php

declare(strict_types=1);

namespace Quicktane\Media;

use Quicktane\Core\Module\ModuleServiceProvider;
use Quicktane\Media\Contracts\ImageFacade as ImageFacadeContract;
use Quicktane\Media\Contracts\MediaFacade as MediaFacadeContract;
use Quicktane\Media\Facades\ImageFacade;
use Quicktane\Media\Facades\MediaFacade;
use Quicktane\Media\Repositories\MediaFileRepository;
use Quicktane\Media\Repositories\MediaVariantRepository;
use Quicktane\Media\Repositories\MysqlMediaFileRepository;
use Quicktane\Media\Repositories\MysqlMediaVariantRepository;

class MediaServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'media';
    }

    public function register(): void
    {
        $this->app->bind(MediaFileRepository::class, MysqlMediaFileRepository::class);
        $this->app->bind(MediaVariantRepository::class, MysqlMediaVariantRepository::class);

        $this->app->bind(MediaFacadeContract::class, MediaFacade::class);
        $this->app->bind(ImageFacadeContract::class, ImageFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();
    }
}
