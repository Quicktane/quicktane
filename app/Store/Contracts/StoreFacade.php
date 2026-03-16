<?php

declare(strict_types=1);

namespace App\Store\Contracts;

use App\Store\Models\Store;
use App\Store\Models\StoreView;
use App\Store\Models\Website;
use Illuminate\Support\Collection;

interface StoreFacade
{
    public function getWebsite(string $uuid): ?Website;

    public function getStore(string $uuid): ?Store;

    public function getStoreView(string $uuid): ?StoreView;

    public function listWebsites(): Collection;

    public function listStores(?int $websiteId = null): Collection;

    public function listStoreViews(?int $storeId = null): Collection;
}
