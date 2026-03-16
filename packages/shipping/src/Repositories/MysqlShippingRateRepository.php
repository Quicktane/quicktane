<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Shipping\Models\ShippingRate;

class MysqlShippingRateRepository implements ShippingRateRepository
{
    public function __construct(
        private readonly ShippingRate $shippingRateModel,
    ) {}

    public function findById(int $id): ?ShippingRate
    {
        return $this->shippingRateModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?ShippingRate
    {
        return $this->shippingRateModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByMethodAndZone(int $methodId, int $zoneId): Collection
    {
        return $this->shippingRateModel->newQuery()
            ->where('shipping_method_id', $methodId)
            ->where('shipping_zone_id', $zoneId)
            ->where('is_active', true)
            ->get();
    }

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = $this->shippingRateModel->newQuery();

        if (isset($filters['shipping_method_id'])) {
            $query->where('shipping_method_id', (int) $filters['shipping_method_id']);
        }

        if (isset($filters['shipping_zone_id'])) {
            $query->where('shipping_zone_id', (int) $filters['shipping_zone_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): ShippingRate
    {
        return $this->shippingRateModel->newQuery()->create($data);
    }

    public function update(ShippingRate $shippingRate, array $data): ShippingRate
    {
        $shippingRate->update($data);

        return $shippingRate;
    }

    public function delete(ShippingRate $shippingRate): bool
    {
        return (bool) $shippingRate->delete();
    }
}
