<?php

declare(strict_types=1);

namespace App\Store\Repositories;

use App\Store\Models\Configuration;
use Illuminate\Support\Collection;

interface ConfigurationRepository
{
    public function getValue(string $path, string $scope = 'global', int $scopeId = 0): ?Configuration;

    public function getValuesForScope(string $scope, int $scopeId): Collection;

    public function setValue(string $path, ?string $value, string $scope = 'global', int $scopeId = 0): Configuration;

    public function deleteValue(string $path, string $scope, int $scopeId): bool;
}
