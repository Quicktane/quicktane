<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Shipping\Models\ShippingMethod;

class MysqlShippingMethodRepository implements ShippingMethodRepository
{
    public function __construct(
        private readonly ShippingMethod $shippingMethodModel,
    ) {}

    public function findById(int $id): ?ShippingMethod
    {
        return $this->shippingMethodModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?ShippingMethod
    {
        return $this->shippingMethodModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCode(string $code): ?ShippingMethod
    {
        return $this->shippingMethodModel->newQuery()->where('code', $code)->first();
    }

    public function findActive(): Collection
    {
        return $this->shippingMethodModel->newQuery()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = $this->shippingMethodModel->newQuery();

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['carrier_code'])) {
            $query->where('carrier_code', $filters['carrier_code']);
        }

        return $query->orderBy('sort_order')->paginate($perPage);
    }

    public function create(array $data): ShippingMethod
    {
        return $this->shippingMethodModel->newQuery()->create($data);
    }

    public function update(ShippingMethod $shippingMethod, array $data): ShippingMethod
    {
        $shippingMethod->update($data);

        return $shippingMethod;
    }

    public function delete(ShippingMethod $shippingMethod): bool
    {
        return (bool) $shippingMethod->delete();
    }
}
