<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Illuminate\Support\Carbon;
use Quicktane\Promotion\Models\CouponUsage;

class MysqlCouponUsageRepository implements CouponUsageRepository
{
    public function __construct(
        private readonly CouponUsage $couponUsageModel,
    ) {}

    public function create(array $data): CouponUsage
    {
        $data['created_at'] = Carbon::now();

        return $this->couponUsageModel->newQuery()->create($data);
    }

    public function countByCustomerAndCoupon(int $customerId, int $couponId): int
    {
        return $this->couponUsageModel->newQuery()
            ->where('customer_id', $customerId)
            ->where('coupon_id', $couponId)
            ->count();
    }
}
