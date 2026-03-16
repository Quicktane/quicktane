<?php

declare(strict_types=1);

namespace Quicktane\Search\Console;

use Illuminate\Console\Command;
use Quicktane\Search\Services\IndexService;

class SearchReindexCommand extends Command
{
    protected $signature = 'search:reindex';

    protected $description = 'Reindex all products in the search engine';

    public function handle(IndexService $indexService): int
    {
        $this->info('Reindexing products...');

        $indexService->reindexProducts();

        $this->info('Product reindexing complete.');

        return self::SUCCESS;
    }
}
