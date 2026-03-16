<?php

declare(strict_types=1);

namespace App\Directory\Repositories;

use App\Directory\Models\Country;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MysqlCountryRepository implements CountryRepository
{
    public function all(bool $activeOnly = false): Collection
    {
        $query = Country::query()->orderBy('sort_order')->orderBy('name');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->get();
    }

    public function findByIso2(string $iso2): ?Country
    {
        return Country::where('iso2', $iso2)->first();
    }

    public function findById(int $id): ?Country
    {
        return Country::find($id);
    }

    public function update(Country $country, array $data): Country
    {
        $country->update($data);

        return $country->refresh();
    }

    public function getForStoreView(int $storeViewId): Collection
    {
        $countryIds = DB::table('store_view_countries')
            ->where('store_view_id', $storeViewId)
            ->pluck('country_id');

        if ($countryIds->isEmpty()) {
            return Country::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        }

        return Country::whereIn('id', $countryIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
