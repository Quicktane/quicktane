<?php

declare(strict_types=1);

namespace App\Directory\Repositories;

use App\Directory\Models\Region;
use Illuminate\Support\Collection;

interface RegionRepository
{
    public function getByCountry(int $countryId, bool $activeOnly = false): Collection;

    public function findById(int $id): ?Region;

    public function update(Region $region, array $data): Region;
}
