<?php

declare(strict_types=1);

namespace Quicktane\Tax\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Tax\Enums\TaxClassType;
use Quicktane\Tax\Models\TaxClass;

interface TaxClassRepository
{
    public function findById(int $id): ?TaxClass;

    public function findByUuid(string $uuid): ?TaxClass;

    public function findDefault(TaxClassType $type): ?TaxClass;

    public function findByType(TaxClassType $type): Collection;

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): TaxClass;

    public function update(TaxClass $taxClass, array $data): TaxClass;

    public function delete(TaxClass $taxClass): void;
}
