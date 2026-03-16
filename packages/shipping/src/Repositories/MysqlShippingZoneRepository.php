<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Shipping\Models\ShippingZone;

class MysqlShippingZoneRepository implements ShippingZoneRepository
{
    public function __construct(
        private readonly ShippingZone $shippingZoneModel,
    ) {}

    public function findById(int $id): ?ShippingZone
    {
        return $this->shippingZoneModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?ShippingZone
    {
        return $this->shippingZoneModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCountryAndRegion(int $countryId, ?int $regionId): ?ShippingZone
    {
        $query = $this->shippingZoneModel->newQuery()
            ->where('is_active', true)
            ->whereHas('countries', function ($countriesQuery) use ($countryId, $regionId): void {
                $countriesQuery->where('country_id', $countryId);

                if ($regionId !== null) {
                    $countriesQuery->where(function ($regionQuery) use ($regionId): void {
                        $regionQuery->where('region_id', $regionId)
                            ->orWhereNull('region_id');
                    });
                } else {
                    $countriesQuery->whereNull('region_id');
                }
            });

        return $query->first();
    }

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = $this->shippingZoneModel->newQuery();

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): ShippingZone
    {
        return $this->shippingZoneModel->newQuery()->create($data);
    }

    public function update(ShippingZone $shippingZone, array $data): ShippingZone
    {
        $shippingZone->update($data);

        return $shippingZone;
    }

    public function delete(ShippingZone $shippingZone): bool
    {
        return (bool) $shippingZone->delete();
    }
}
