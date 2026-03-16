<?php

declare(strict_types=1);

namespace App\Directory\Contracts;

use App\Directory\Models\Country;
use Illuminate\Support\Collection;

interface CountryFacade
{
    public function listCountries(bool $activeOnly = false): Collection;

    public function getCountry(string $iso2): ?Country;

    public function getRegionsByCountry(string $iso2): Collection;

    public function availableForStoreView(int $storeViewId): Collection;
}
