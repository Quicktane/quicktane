<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Shipping\Models\ShippingMethod;

interface ShippingMethodRepository
{
    public function findById(int $id): ?ShippingMethod;

    public function findByUuid(string $uuid): ?ShippingMethod;

    public function findByCode(string $code): ?ShippingMethod;

    public function findActive(): Collection;

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator;

    public function create(array $data): ShippingMethod;

    public function update(ShippingMethod $shippingMethod, array $data): ShippingMethod;

    public function delete(ShippingMethod $shippingMethod): bool;
}
