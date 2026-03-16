<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Quicktane\Promotion\Models\CartPriceRule;

class MysqlCartPriceRuleRepository implements CartPriceRuleRepository
{
    public function __construct(
        private readonly CartPriceRule $cartPriceRuleModel,
    ) {}

    public function findById(int $id): ?CartPriceRule
    {
        return $this->cartPriceRuleModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?CartPriceRule
    {
        return $this->cartPriceRuleModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findActive(): Collection
    {
        $today = Carbon::today();

        return $this->cartPriceRuleModel->newQuery()
            ->where('is_active', true)
            ->where(function ($query) use ($today): void {
                $query->whereNull('from_date')
                    ->orWhere('from_date', '<=', $today);
            })
            ->where(function ($query) use ($today): void {
                $query->whereNull('to_date')
                    ->orWhere('to_date', '>=', $today);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->cartPriceRuleModel->newQuery();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): CartPriceRule
    {
        return $this->cartPriceRuleModel->newQuery()->create($data);
    }

    public function update(CartPriceRule $cartPriceRule, array $data): CartPriceRule
    {
        $cartPriceRule->update($data);

        return $cartPriceRule;
    }

    public function delete(CartPriceRule $cartPriceRule): void
    {
        $cartPriceRule->delete();
    }
}
