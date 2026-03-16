<?php

declare(strict_types=1);

namespace Quicktane\Tax\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Tax\Models\TaxZone;
use Quicktane\Tax\Models\TaxZoneRule;

class MysqlTaxZoneRepository implements TaxZoneRepository
{
    public function __construct(
        private readonly TaxZone $taxZoneModel,
        private readonly TaxZoneRule $taxZoneRuleModel,
    ) {}

    public function findById(int $id): ?TaxZone
    {
        return $this->taxZoneModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?TaxZone
    {
        return $this->taxZoneModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByAddress(int $countryId, ?int $regionId, ?string $postcode): ?TaxZone
    {
        $query = $this->taxZoneModel->newQuery()
            ->where('is_active', true)
            ->whereHas('zoneRules', function ($query) use ($countryId, $regionId, $postcode): void {
                $query->where('country_id', $countryId);

                if ($regionId !== null) {
                    $query->where(function ($query) use ($regionId): void {
                        $query->where('region_id', $regionId)
                            ->orWhereNull('region_id');
                    });
                }

                if ($postcode !== null) {
                    $query->where(function ($query) use ($postcode): void {
                        $query->where(function ($query) use ($postcode): void {
                            $query->where('postcode_from', '<=', $postcode)
                                ->where('postcode_to', '>=', $postcode);
                        })->orWhere(function ($query): void {
                            $query->whereNull('postcode_from')
                                ->whereNull('postcode_to');
                        });
                    });
                }
            })
            ->orderBy('sort_order');

        return $query->first();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->taxZoneModel->newQuery()->with('zoneRules');

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('sort_order')->paginate($perPage);
    }

    public function create(array $data): TaxZone
    {
        return $this->taxZoneModel->newQuery()->create($data);
    }

    public function update(TaxZone $taxZone, array $data): TaxZone
    {
        $taxZone->update($data);

        return $taxZone;
    }

    public function delete(TaxZone $taxZone): void
    {
        $taxZone->delete();
    }

    public function syncZoneRules(TaxZone $taxZone, array $rules): void
    {
        $this->taxZoneRuleModel->newQuery()->where('tax_zone_id', $taxZone->id)->delete();

        foreach ($rules as $rule) {
            $this->taxZoneRuleModel->newQuery()->create([
                'tax_zone_id' => $taxZone->id,
                'country_id' => $rule['country_id'] ?? null,
                'region_id' => $rule['region_id'] ?? null,
                'postcode_from' => $rule['postcode_from'] ?? null,
                'postcode_to' => $rule['postcode_to'] ?? null,
            ]);
        }
    }
}
