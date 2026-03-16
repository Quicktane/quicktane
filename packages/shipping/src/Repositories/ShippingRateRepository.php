<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Shipping\Models\ShippingRate;

interface ShippingRateRepository
{
    public function findById(int $id): ?ShippingRate;

    public function findByUuid(string $uuid): ?ShippingRate;

    public function findByMethodAndZone(int $methodId, int $zoneId): Collection;

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator;

    public function create(array $data): ShippingRate;

    public function update(ShippingRate $shippingRate, array $data): ShippingRate;

    public function delete(ShippingRate $shippingRate): bool;
}
