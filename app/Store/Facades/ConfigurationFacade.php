<?php

declare(strict_types=1);

namespace App\Store\Facades;

use App\Store\Contracts\ConfigurationFacade as ConfigurationFacadeContract;
use App\Store\Repositories\ConfigurationRepository;
use App\Store\Services\ConfigurationResolver;
use Illuminate\Support\Collection;

class ConfigurationFacade implements ConfigurationFacadeContract
{
    public function __construct(
        private readonly ConfigurationRepository $configurationRepository,
        private readonly ConfigurationResolver $configurationResolver,
    ) {}

    public function getValue(string $path, string $scopeType = 'global', int $scopeId = 0, mixed $default = null): mixed
    {
        return $this->configurationResolver->resolve($path, $scopeType, $scopeId, $default);
    }

    public function setValue(string $path, ?string $value, string $scope = 'global', int $scopeId = 0): void
    {
        $this->configurationRepository->setValue($path, $value, $scope, $scopeId);
    }

    public function deleteValue(string $path, string $scope, int $scopeId): void
    {
        $this->configurationRepository->deleteValue($path, $scope, $scopeId);
    }

    public function getValuesForScope(string $scope, int $scopeId): Collection
    {
        return $this->configurationRepository->getValuesForScope($scope, $scopeId);
    }
}
