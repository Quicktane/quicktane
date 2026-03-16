<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Facades;

use Illuminate\Support\Collection;
use Quicktane\Promotion\Contracts\PromotionFacade as PromotionFacadeContract;
use Quicktane\Promotion\DataTransferObjects\CouponValidationResult;
use Quicktane\Promotion\DataTransferObjects\PromotionResult;
use Quicktane\Promotion\Services\PromotionService;

class PromotionFacade implements PromotionFacadeContract
{
    public function __construct(
        private readonly PromotionService $promotionService,
    ) {}

    public function applyRules(int $cartId, ?int $customerId = null, ?string $couponCode = null): PromotionResult
    {
        return $this->promotionService->applyRules($cartId, $customerId, $couponCode);
    }

    public function validateCoupon(string $code, int $cartId, ?int $customerId = null): CouponValidationResult
    {
        return $this->promotionService->validateCoupon($code, $cartId, $customerId);
    }

    public function getActiveRules(): Collection
    {
        return $this->promotionService->getActiveRules();
    }

    public function recordUsage(int $ruleId, ?int $couponId, ?int $customerId, int $orderId, string $discountAmount): void
    {
        $this->promotionService->recordUsage($ruleId, $couponId, $customerId, $orderId, $discountAmount);
    }
}
