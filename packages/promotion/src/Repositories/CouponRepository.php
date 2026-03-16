<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Promotion\Models\Coupon;

interface CouponRepository
{
    public function findById(int $id): ?Coupon;

    public function findByUuid(string $uuid): ?Coupon;

    public function findByCode(string $code): ?Coupon;

    public function findByRule(int $ruleId): Collection;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Coupon;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Coupon $coupon, array $data): Coupon;

    public function delete(Coupon $coupon): void;

    public function incrementUsage(int $couponId): void;
}
