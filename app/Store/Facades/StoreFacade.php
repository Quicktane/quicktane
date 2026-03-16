<?php

declare(strict_types=1);

namespace App\Store\Facades;

use App\Store\Contracts\StoreFacade as StoreFacadeContract;
use App\Store\Models\Store;
use App\Store\Models\StoreView;
use App\Store\Models\Website;
use App\Store\Repositories\StoreRepository;
use App\Store\Repositories\StoreViewRepository;
use App\Store\Repositories\WebsiteRepository;
use Illuminate\Support\Collection;

class StoreFacade implements StoreFacadeContract
{
    public function __construct(
        private readonly WebsiteRepository $websiteRepository,
        private readonly StoreRepository $storeRepository,
        private readonly StoreViewRepository $storeViewRepository,
    ) {}

    public function getWebsite(string $uuid): ?Website
    {
        return $this->websiteRepository->findByUuid($uuid);
    }

    public function getStore(string $uuid): ?Store
    {
        return $this->storeRepository->findByUuid($uuid);
    }

    public function getStoreView(string $uuid): ?StoreView
    {
        return $this->storeViewRepository->findByUuid($uuid);
    }

    public function listWebsites(): Collection
    {
        return $this->websiteRepository->all();
    }

    public function listStores(?int $websiteId = null): Collection
    {
        if ($websiteId !== null) {
            return $this->storeRepository->getByWebsite($websiteId);
        }

        return $this->storeRepository->all();
    }

    public function listStoreViews(?int $storeId = null): Collection
    {
        if ($storeId !== null) {
            return $this->storeViewRepository->getByStore($storeId);
        }

        return $this->storeViewRepository->all();
    }
}
