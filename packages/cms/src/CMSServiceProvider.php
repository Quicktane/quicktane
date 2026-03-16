<?php

declare(strict_types=1);

namespace Quicktane\CMS;

use App\Catalog\Events\AfterCategoryCreate;
use App\Catalog\Events\AfterCategoryUpdate;
use App\Catalog\Events\AfterProductCreate;
use App\Catalog\Events\AfterProductUpdate;
use Illuminate\Support\Facades\Event;
use Quicktane\CMS\Contracts\BlockFacade as BlockFacadeContract;
use Quicktane\CMS\Contracts\PageFacade as PageFacadeContract;
use Quicktane\CMS\Contracts\UrlFacade as UrlFacadeContract;
use Quicktane\CMS\Facades\BlockFacade;
use Quicktane\CMS\Facades\PageFacade;
use Quicktane\CMS\Facades\UrlFacade;
use Quicktane\CMS\Listeners\GenerateCategoryUrlRewrite;
use Quicktane\CMS\Listeners\GenerateProductUrlRewrite;
use Quicktane\CMS\Repositories\BlockRepository;
use Quicktane\CMS\Repositories\MysqlBlockRepository;
use Quicktane\CMS\Repositories\MysqlPageRepository;
use Quicktane\CMS\Repositories\MysqlUrlRewriteRepository;
use Quicktane\CMS\Repositories\PageRepository;
use Quicktane\CMS\Repositories\UrlRewriteRepository;
use Quicktane\Core\Module\ModuleServiceProvider;

class CMSServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'cms';
    }

    public function register(): void
    {
        $this->app->bind(PageRepository::class, MysqlPageRepository::class);
        $this->app->bind(BlockRepository::class, MysqlBlockRepository::class);
        $this->app->bind(UrlRewriteRepository::class, MysqlUrlRewriteRepository::class);

        $this->app->bind(PageFacadeContract::class, PageFacade::class);
        $this->app->bind(BlockFacadeContract::class, BlockFacade::class);
        $this->app->bind(UrlFacadeContract::class, UrlFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();

        $this->registerEventListeners();
    }

    private function registerEventListeners(): void
    {
        Event::listen(AfterProductCreate::class, [GenerateProductUrlRewrite::class, 'handleCreate']);
        Event::listen(AfterProductUpdate::class, [GenerateProductUrlRewrite::class, 'handleUpdate']);
        Event::listen(AfterCategoryCreate::class, [GenerateCategoryUrlRewrite::class, 'handleCreate']);
        Event::listen(AfterCategoryUpdate::class, [GenerateCategoryUrlRewrite::class, 'handleUpdate']);
    }
}
