<?php

declare(strict_types=1);

namespace Quicktane\Tax\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Tax\Enums\TaxClassType;
use Quicktane\Tax\Models\TaxClass;

class MysqlTaxClassRepository implements TaxClassRepository
{
    public function __construct(
        private readonly TaxClass $taxClassModel,
    ) {}

    public function findById(int $id): ?TaxClass
    {
        return $this->taxClassModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?TaxClass
    {
        return $this->taxClassModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findDefault(TaxClassType $type): ?TaxClass
    {
        return $this->taxClassModel->newQuery()
            ->where('type', $type)
            ->where('is_default', true)
            ->first();
    }

    public function findByType(TaxClassType $type): Collection
    {
        return $this->taxClassModel->newQuery()
            ->where('type', $type)
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->taxClassModel->newQuery();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): TaxClass
    {
        return $this->taxClassModel->newQuery()->create($data);
    }

    public function update(TaxClass $taxClass, array $data): TaxClass
    {
        $taxClass->update($data);

        return $taxClass;
    }

    public function delete(TaxClass $taxClass): void
    {
        $taxClass->delete();
    }
}
