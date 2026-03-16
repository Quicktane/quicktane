<?php

declare(strict_types=1);

namespace App\Store\Contracts;

use Illuminate\Support\Collection;

interface ConfigurationFacade
{
    public function getValue(string $path, string $scopeType = 'global', int $scopeId = 0, mixed $default = null): mixed;

    public function setValue(string $path, ?string $value, string $scope = 'global', int $scopeId = 0): void;

    public function deleteValue(string $path, string $scope, int $scopeId): void;

    public function getValuesForScope(string $scope, int $scopeId): Collection;
}
