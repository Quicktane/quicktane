<?php

declare(strict_types=1);

namespace Quicktane\Tax\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Tax\Models\TaxRule;

interface TaxRuleRepository
{
    public function findById(int $id): ?TaxRule;

    public function findByUuid(string $uuid): ?TaxRule;

    public function findByTaxClasses(int $productTaxClassId, int $customerTaxClassId): Collection;

    public function findActiveByTaxClasses(int $productTaxClassId, int $customerTaxClassId): Collection;

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): TaxRule;

    public function update(TaxRule $taxRule, array $data): TaxRule;

    public function delete(TaxRule $taxRule): void;
}
