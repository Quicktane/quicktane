<?php

declare(strict_types=1);

namespace App\Directory\Repositories;

use App\Directory\Models\Region;
use Illuminate\Support\Collection;

class MysqlRegionRepository implements RegionRepository
{
    public function getByCountry(int $countryId, bool $activeOnly = false): Collection
    {
        $query = Region::where('country_id', $countryId)
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->get();
    }

    public function findById(int $id): ?Region
    {
        return Region::find($id);
    }

    public function update(Region $region, array $data): Region
    {
        $region->update($data);

        return $region->refresh();
    }
}
