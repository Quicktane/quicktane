<?php

declare(strict_types=1);

namespace Quicktane\Tax\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Tax\Models\TaxRate;

class MysqlTaxRateRepository implements TaxRateRepository
{
    public function __construct(
        private readonly TaxRate $taxRateModel,
    ) {}

    public function findById(int $id): ?TaxRate
    {
        return $this->taxRateModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?TaxRate
    {
        return $this->taxRateModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByZone(int $taxZoneId): Collection
    {
        return $this->taxRateModel->newQuery()
            ->where('tax_zone_id', $taxZoneId)
            ->orderBy('priority')
            ->get();
    }

    public function findActiveByZone(int $taxZoneId): Collection
    {
        return $this->taxRateModel->newQuery()
            ->where('tax_zone_id', $taxZoneId)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->taxRateModel->newQuery()->with('zone');

        if (isset($filters['tax_zone_id'])) {
            $query->where('tax_zone_id', $filters['tax_zone_id']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('priority')->paginate($perPage);
    }

    public function create(array $data): TaxRate
    {
        return $this->taxRateModel->newQuery()->create($data);
    }

    public function update(TaxRate $taxRate, array $data): TaxRate
    {
        $taxRate->update($data);

        return $taxRate;
    }

    public function delete(TaxRate $taxRate): void
    {
        $taxRate->delete();
    }
}
