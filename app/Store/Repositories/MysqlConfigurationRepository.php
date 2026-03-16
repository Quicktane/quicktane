<?php

declare(strict_types=1);

namespace App\Store\Repositories;

use App\Store\Models\Configuration;
use Illuminate\Support\Collection;

class MysqlConfigurationRepository implements ConfigurationRepository
{
    public function __construct(
        private readonly Configuration $configurationModel,
    ) {}

    public function getValue(string $path, string $scope = 'global', int $scopeId = 0): ?Configuration
    {
        return $this->configurationModel->newQuery()
            ->where('scope', $scope)
            ->where('scope_id', $scopeId)
            ->where('path', $path)
            ->first();
    }

    public function getValuesForScope(string $scope, int $scopeId): Collection
    {
        return $this->configurationModel->newQuery()
            ->where('scope', $scope)
            ->where('scope_id', $scopeId)
            ->get();
    }

    public function setValue(string $path, ?string $value, string $scope = 'global', int $scopeId = 0): Configuration
    {
        return $this->configurationModel->newQuery()->updateOrCreate(
            [
                'scope' => $scope,
                'scope_id' => $scopeId,
                'path' => $path,
            ],
            [
                'value' => $value,
            ],
        );
    }

    public function deleteValue(string $path, string $scope, int $scopeId): bool
    {
        return (bool) $this->configurationModel->newQuery()
            ->where('scope', $scope)
            ->where('scope_id', $scopeId)
            ->where('path', $path)
            ->delete();
    }
}
