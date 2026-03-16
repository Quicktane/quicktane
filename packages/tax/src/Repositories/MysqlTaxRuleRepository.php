<?php

declare(strict_types=1);

namespace Quicktane\Tax\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Tax\Models\TaxRule;

class MysqlTaxRuleRepository implements TaxRuleRepository
{
    public function __construct(
        private readonly TaxRule $taxRuleModel,
    ) {}

    public function findById(int $id): ?TaxRule
    {
        return $this->taxRuleModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?TaxRule
    {
        return $this->taxRuleModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByTaxClasses(int $productTaxClassId, int $customerTaxClassId): Collection
    {
        return $this->taxRuleModel->newQuery()
            ->where('product_tax_class_id', $productTaxClassId)
            ->where('customer_tax_class_id', $customerTaxClassId)
            ->orderBy('priority')
            ->get();
    }

    public function findActiveByTaxClasses(int $productTaxClassId, int $customerTaxClassId): Collection
    {
        return $this->taxRuleModel->newQuery()
            ->where('product_tax_class_id', $productTaxClassId)
            ->where('customer_tax_class_id', $customerTaxClassId)
            ->where('is_active', true)
            ->with('taxRate')
            ->orderBy('priority')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->taxRuleModel->newQuery()->with(['taxRate', 'productTaxClass', 'customerTaxClass']);

        if (isset($filters['product_tax_class_id'])) {
            $query->where('product_tax_class_id', $filters['product_tax_class_id']);
        }

        if (isset($filters['customer_tax_class_id'])) {
            $query->where('customer_tax_class_id', $filters['customer_tax_class_id']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('priority')->paginate($perPage);
    }

    public function create(array $data): TaxRule
    {
        return $this->taxRuleModel->newQuery()->create($data);
    }

    public function update(TaxRule $taxRule, array $data): TaxRule
    {
        $taxRule->update($data);

        return $taxRule;
    }

    public function delete(TaxRule $taxRule): void
    {
        $taxRule->delete();
    }
}
