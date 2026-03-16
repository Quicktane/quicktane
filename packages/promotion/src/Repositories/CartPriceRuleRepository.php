<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Promotion\Models\CartPriceRule;

interface CartPriceRuleRepository
{
    public function findById(int $id): ?CartPriceRule;

    public function findByUuid(string $uuid): ?CartPriceRule;

    public function findActive(): Collection;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): CartPriceRule;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(CartPriceRule $cartPriceRule, array $data): CartPriceRule;

    public function delete(CartPriceRule $cartPriceRule): void;
}
