<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Shipping\Models\ShippingZone;

interface ShippingZoneRepository
{
    public function findById(int $id): ?ShippingZone;

    public function findByUuid(string $uuid): ?ShippingZone;

    public function findByCountryAndRegion(int $countryId, ?int $regionId): ?ShippingZone;

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator;

    public function create(array $data): ShippingZone;

    public function update(ShippingZone $shippingZone, array $data): ShippingZone;

    public function delete(ShippingZone $shippingZone): bool;
}
