<?php

declare(strict_types=1);

namespace App\Directory\Facades;

use App\Directory\Contracts\CountryFacade as CountryFacadeContract;
use App\Directory\Models\Country;
use App\Directory\Repositories\CountryRepository;
use App\Directory\Repositories\RegionRepository;
use Illuminate\Support\Collection;

class CountryFacade implements CountryFacadeContract
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly RegionRepository $regionRepository,
    ) {}

    public function listCountries(bool $activeOnly = false): Collection
    {
        return $this->countryRepository->all($activeOnly);
    }

    public function getCountry(string $iso2): ?Country
    {
        return $this->countryRepository->findByIso2($iso2);
    }

    public function getRegionsByCountry(string $iso2): Collection
    {
        $country = $this->countryRepository->findByIso2($iso2);

        if ($country === null) {
            return collect();
        }

        return $this->regionRepository->getByCountry($country->id);
    }

    public function availableForStoreView(int $storeViewId): Collection
    {
        return $this->countryRepository->getForStoreView($storeViewId);
    }
}
