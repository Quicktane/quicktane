<?php

declare(strict_types=1);

namespace App\Store\Services;

use App\Store\Repositories\ConfigurationRepository;
use App\Store\Repositories\StoreRepository;
use App\Store\Repositories\StoreViewRepository;

class ConfigurationResolver
{
    public function __construct(
        private readonly ConfigurationRepository $configurationRepository,
        private readonly StoreViewRepository $storeViewRepository,
        private readonly StoreRepository $storeRepository,
    ) {}

    public function resolve(string $path, string $scopeType = 'global', int $scopeId = 0, mixed $default = null): mixed
    {
        $scopes = $this->buildScopeChain($scopeType, $scopeId);

        foreach ($scopes as [$scope, $id]) {
            $configuration = $this->configurationRepository->getValue($path, $scope, $id);

            if ($configuration !== null) {
                return $configuration->value;
            }
        }

        return $default;
    }

    /**
     * @return list<array{0: string, 1: int}>
     */
    private function buildScopeChain(string $scopeType, int $scopeId): array
    {
        return match ($scopeType) {
            'store_view' => $this->buildStoreViewChain($scopeId),
            'store' => $this->buildStoreChain($scopeId),
            'website' => $this->buildWebsiteChain($scopeId),
            default => [['global', 0]],
        };
    }

    /**
     * @return list<array{0: string, 1: int}>
     */
    private function buildStoreViewChain(int $storeViewId): array
    {
        $chain = [['store_view', $storeViewId]];

        $storeView = $this->storeViewRepository->findById($storeViewId);

        if ($storeView !== null) {
            $chain = array_merge($chain, $this->buildStoreChain($storeView->store_id));
        } else {
            $chain[] = ['global', 0];
        }

        return $chain;
    }

    /**
     * @return list<array{0: string, 1: int}>
     */
    private function buildStoreChain(int $storeId): array
    {
        $chain = [['store', $storeId]];

        $store = $this->storeRepository->findById($storeId);

        if ($store !== null) {
            $chain[] = ['website', $store->website_id];
        }

        $chain[] = ['global', 0];

        return $chain;
    }

    /**
     * @return list<array{0: string, 1: int}>
     */
    private function buildWebsiteChain(int $websiteId): array
    {
        return [
            ['website', $websiteId],
            ['global', 0],
        ];
    }
}
