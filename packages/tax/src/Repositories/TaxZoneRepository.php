<?php

declare(strict_types=1);

namespace Quicktane\Tax\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Tax\Models\TaxZone;

interface TaxZoneRepository
{
    public function findById(int $id): ?TaxZone;

    public function findByUuid(string $uuid): ?TaxZone;

    public function findByAddress(int $countryId, ?int $regionId, ?string $postcode): ?TaxZone;

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): TaxZone;

    public function update(TaxZone $taxZone, array $data): TaxZone;

    public function delete(TaxZone $taxZone): void;

    public function syncZoneRules(TaxZone $taxZone, array $rules): void;
}
