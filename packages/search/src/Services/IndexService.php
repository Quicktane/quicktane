<?php

declare(strict_types=1);

namespace Quicktane\Search\Services;

use App\Catalog\Models\Product;
use Illuminate\Support\Facades\Artisan;

class IndexService
{
    public function reindexProducts(): void
    {
        Artisan::call('scout:import', ['model' => Product::class]);
    }

    public function flushProducts(): void
    {
        Artisan::call('scout:flush', ['model' => Product::class]);
    }

    public function syncSettings(): void
    {
        Artisan::call('scout:sync-index-settings');
    }
}
