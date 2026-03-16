<?php

declare(strict_types=1);

namespace Quicktane\Search;

use Quicktane\Core\Module\ModuleServiceProvider;
use Quicktane\Search\Console\SearchReindexCommand;
use Quicktane\Search\Contracts\SearchFacade as SearchFacadeContract;
use Quicktane\Search\Facades\SearchFacade;
use Quicktane\Search\Repositories\MysqlSearchSynonymRepository;
use Quicktane\Search\Repositories\SearchSynonymRepository;

class SearchServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'search';
    }

    public function register(): void
    {
        $this->app->bind(SearchSynonymRepository::class, MysqlSearchSynonymRepository::class);

        $this->app->bind(SearchFacadeContract::class, SearchFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();

        if ($this->app->runningInConsole()) {
            $this->commands([
                SearchReindexCommand::class,
            ]);
        }
    }
}
