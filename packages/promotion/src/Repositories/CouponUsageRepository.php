<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Quicktane\Promotion\Models\CouponUsage;

interface CouponUsageRepository
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): CouponUsage;

    public function countByCustomerAndCoupon(int $customerId, int $couponId): int;
}
