<?php

declare(strict_types=1);

namespace Quicktane\Tax\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Tax\Models\TaxRate;

interface TaxRateRepository
{
    public function findById(int $id): ?TaxRate;

    public function findByUuid(string $uuid): ?TaxRate;

    public function findByZone(int $taxZoneId): Collection;

    public function findActiveByZone(int $taxZoneId): Collection;

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): TaxRate;

    public function update(TaxRate $taxRate, array $data): TaxRate;

    public function delete(TaxRate $taxRate): void;
}
