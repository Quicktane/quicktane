<?php

declare(strict_types=1);

namespace App\Directory\Repositories;

use App\Directory\Models\Country;
use Illuminate\Support\Collection;

interface CountryRepository
{
    public function all(bool $activeOnly = false): Collection;

    public function findByIso2(string $iso2): ?Country;

    public function findById(int $id): ?Country;

    public function update(Country $country, array $data): Country;

    public function getForStoreView(int $storeViewId): Collection;
}
