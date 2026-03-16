<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Quicktane\Promotion\Models\Coupon;

class MysqlCouponRepository implements CouponRepository
{
    public function __construct(
        private readonly Coupon $couponModel,
    ) {}

    public function findById(int $id): ?Coupon
    {
        return $this->couponModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Coupon
    {
        return $this->couponModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCode(string $code): ?Coupon
    {
        return $this->couponModel->newQuery()
            ->where('code', $code)
            ->with('rule')
            ->first();
    }

    public function findByRule(int $ruleId): Collection
    {
        return $this->couponModel->newQuery()
            ->where('cart_price_rule_id', $ruleId)
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->couponModel->newQuery()->with('rule');

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['cart_price_rule_id'])) {
            $query->where('cart_price_rule_id', $filters['cart_price_rule_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('code', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Coupon
    {
        return $this->couponModel->newQuery()->create($data);
    }

    public function update(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);

        return $coupon;
    }

    public function delete(Coupon $coupon): void
    {
        $coupon->delete();
    }

    public function incrementUsage(int $couponId): void
    {
        $this->couponModel->newQuery()
            ->where('id', $couponId)
            ->increment('times_used');
    }
}
