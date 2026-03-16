<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Contracts;

use Illuminate\Support\Collection;
use Quicktane\Promotion\DataTransferObjects\CouponValidationResult;
use Quicktane\Promotion\DataTransferObjects\PromotionResult;

interface PromotionFacade
{
    public function applyRules(int $cartId, ?int $customerId = null, ?string $couponCode = null): PromotionResult;

    public function validateCoupon(string $code, int $cartId, ?int $customerId = null): CouponValidationResult;

    public function getActiveRules(): Collection;

    public function recordUsage(int $ruleId, ?int $couponId, ?int $customerId, int $orderId, string $discountAmount): void;
}
