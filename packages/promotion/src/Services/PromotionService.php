<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Services;

use App\Cart\Contracts\CartFacade;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Quicktane\Promotion\DataTransferObjects\CouponValidationResult;
use Quicktane\Promotion\DataTransferObjects\DiscountResult;
use Quicktane\Promotion\DataTransferObjects\PromotionContext;
use Quicktane\Promotion\DataTransferObjects\PromotionResult;
use Quicktane\Promotion\Enums\ActionType;
use Quicktane\Promotion\Models\CartPriceRule;
use Quicktane\Promotion\Repositories\CartPriceRuleRepository;
use Quicktane\Promotion\Repositories\CouponRepository;
use Quicktane\Promotion\Repositories\CouponUsageRepository;
use Quicktane\Promotion\Repositories\RuleAppliedHistoryRepository;

class PromotionService
{
    public function __construct(
        private readonly CartPriceRuleRepository $cartPriceRuleRepository,
        private readonly CouponRepository $couponRepository,
        private readonly CouponUsageRepository $couponUsageRepository,
        private readonly RuleAppliedHistoryRepository $ruleAppliedHistoryRepository,
        private readonly ConditionEngine $conditionEngine,
        private readonly CartFacade $cartFacade,
    ) {}

    public function applyRules(int $cartId, ?int $customerId = null, ?string $couponCode = null): PromotionResult
    {
        $activeRules = $this->cartPriceRuleRepository->findActive();
        $promotionContext = $this->buildPromotionContext($cartId, $customerId, $couponCode);

        $discountResults = [];
        $totalDiscount = '0.0000';
        $freeShipping = false;

        foreach ($activeRules as $cartPriceRule) {
            if (! $this->evaluateRuleConditions($cartPriceRule, $promotionContext)) {
                continue;
            }

            $discountResult = $this->calculateDiscount($cartPriceRule, $promotionContext, $couponCode);
            $discountResults[] = $discountResult;

            $totalDiscount = bcadd($totalDiscount, $discountResult->discountAmount, 4);

            if ($cartPriceRule->action_type === ActionType::FreeShipping || $cartPriceRule->apply_to_shipping) {
                $freeShipping = true;
            }

            if ($cartPriceRule->stop_further_processing) {
                break;
            }
        }

        return new PromotionResult(
            totalDiscount: $totalDiscount,
            discounts: $discountResults,
            freeShipping: $freeShipping,
        );
    }

    public function validateCoupon(string $couponCode, int $cartId, ?int $customerId = null): CouponValidationResult
    {
        $coupon = $this->couponRepository->findByCode($couponCode);

        if ($coupon === null) {
            return new CouponValidationResult(
                isValid: false,
                errorMessage: 'Coupon code not found.',
                rule: null,
            );
        }

        if (! $coupon->is_active) {
            return new CouponValidationResult(
                isValid: false,
                errorMessage: 'This coupon is no longer active.',
                rule: null,
            );
        }

        if ($coupon->expires_at !== null && $coupon->expires_at->isPast()) {
            return new CouponValidationResult(
                isValid: false,
                errorMessage: 'This coupon has expired.',
                rule: null,
            );
        }

        if ($coupon->usage_limit !== null && $coupon->times_used >= $coupon->usage_limit) {
            return new CouponValidationResult(
                isValid: false,
                errorMessage: 'This coupon has reached its usage limit.',
                rule: null,
            );
        }

        if ($customerId !== null && $coupon->usage_per_customer !== null) {
            $customerUsageCount = $this->couponUsageRepository->countByCustomerAndCoupon($customerId, $coupon->id);

            if ($customerUsageCount >= $coupon->usage_per_customer) {
                return new CouponValidationResult(
                    isValid: false,
                    errorMessage: 'You have already used this coupon the maximum number of times.',
                    rule: null,
                );
            }
        }

        $cartPriceRule = $coupon->rule;

        if ($cartPriceRule === null || ! $cartPriceRule->is_active) {
            return new CouponValidationResult(
                isValid: false,
                errorMessage: 'The promotion associated with this coupon is no longer active.',
                rule: null,
            );
        }

        $today = Carbon::today();

        if ($cartPriceRule->from_date !== null && $cartPriceRule->from_date->isAfter($today)) {
            return new CouponValidationResult(
                isValid: false,
                errorMessage: 'This coupon is not yet valid.',
                rule: null,
            );
        }

        if ($cartPriceRule->to_date !== null && $cartPriceRule->to_date->isBefore($today)) {
            return new CouponValidationResult(
                isValid: false,
                errorMessage: 'This coupon has expired.',
                rule: null,
            );
        }

        return new CouponValidationResult(
            isValid: true,
            errorMessage: null,
            rule: $cartPriceRule,
        );
    }

    public function getActiveRules(): Collection
    {
        return $this->cartPriceRuleRepository->findActive();
    }

    public function recordUsage(int $ruleId, ?int $couponId, ?int $customerId, int $orderId, string $discountAmount): void
    {
        $cartPriceRule = $this->cartPriceRuleRepository->findById($ruleId);

        if ($cartPriceRule !== null) {
            $this->cartPriceRuleRepository->update($cartPriceRule, [
                'times_used' => $cartPriceRule->times_used + 1,
            ]);
        }

        if ($couponId !== null) {
            $this->couponRepository->incrementUsage($couponId);

            $this->couponUsageRepository->create([
                'coupon_id' => $couponId,
                'customer_id' => $customerId,
                'order_id' => $orderId,
            ]);
        }

        $this->ruleAppliedHistoryRepository->create([
            'cart_price_rule_id' => $ruleId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
        ]);
    }

    private function buildPromotionContext(int $cartId, ?int $customerId, ?string $couponCode): PromotionContext
    {
        $cart = $this->cartFacade->getCartWithItems($cartId);

        $cartItems = [];
        $totalWeight = '0.0000';

        if ($cart !== null && $cart->items !== null) {
            foreach ($cart->items as $cartItem) {
                $cartItems[] = [
                    'product_id' => $cartItem->product_id,
                    'sku' => $cartItem->sku,
                    'category_ids' => [],
                    'quantity' => $cartItem->quantity,
                    'row_total' => $cartItem->row_total,
                    'product_type' => $cartItem->product_type ?? 'simple',
                ];
            }
        }

        return new PromotionContext(
            cartItems: $cartItems,
            subtotal: $cart?->subtotal ?? '0.0000',
            itemsCount: $cart?->items_count ?? 0,
            totalWeight: $totalWeight,
            customerId: $customerId,
            customerGroupId: null,
            couponCode: $couponCode,
        );
    }

    private function evaluateRuleConditions(CartPriceRule $cartPriceRule, PromotionContext $promotionContext): bool
    {
        $rootConditions = $cartPriceRule->conditions()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        if ($rootConditions->isEmpty()) {
            return true;
        }

        foreach ($rootConditions as $rootCondition) {
            if (! $this->conditionEngine->evaluate($rootCondition, $promotionContext)) {
                return false;
            }
        }

        return true;
    }

    private function calculateDiscount(CartPriceRule $cartPriceRule, PromotionContext $promotionContext, ?string $couponCode): DiscountResult
    {
        $discountAmount = match ($cartPriceRule->action_type) {
            ActionType::ByPercent => $this->calculatePercentDiscount($cartPriceRule, $promotionContext),
            ActionType::ByFixed => $this->calculateFixedDiscount($cartPriceRule, $promotionContext),
            ActionType::BuyXGetY => $this->calculateBuyXGetYDiscount($promotionContext),
            ActionType::FreeShipping => '0.0000',
        };

        if ($cartPriceRule->max_discount_amount !== null && bccomp($discountAmount, $cartPriceRule->max_discount_amount, 4) > 0) {
            $discountAmount = $cartPriceRule->max_discount_amount;
        }

        return new DiscountResult(
            ruleId: $cartPriceRule->id,
            ruleName: $cartPriceRule->name,
            discountAmount: $discountAmount,
            actionType: $cartPriceRule->action_type,
            couponCode: $couponCode,
        );
    }

    private function calculatePercentDiscount(CartPriceRule $cartPriceRule, PromotionContext $promotionContext): string
    {
        $totalDiscount = '0.0000';

        foreach ($promotionContext->cartItems as $cartItem) {
            $itemDiscount = bcdiv(
                bcmul($cartItem['row_total'], $cartPriceRule->action_amount, 4),
                '100.0000',
                4,
            );
            $totalDiscount = bcadd($totalDiscount, $itemDiscount, 4);
        }

        return $totalDiscount;
    }

    private function calculateFixedDiscount(CartPriceRule $cartPriceRule, PromotionContext $promotionContext): string
    {
        $actionAmount = $cartPriceRule->action_amount ?? '0.0000';

        if (bccomp($actionAmount, $promotionContext->subtotal, 4) > 0) {
            return $promotionContext->subtotal;
        }

        return $actionAmount;
    }

    private function calculateBuyXGetYDiscount(PromotionContext $promotionContext): string
    {
        if (empty($promotionContext->cartItems)) {
            return '0.0000';
        }

        $cheapestRowTotal = null;
        $cheapestUnitPrice = null;

        foreach ($promotionContext->cartItems as $cartItem) {
            $unitPrice = bcdiv($cartItem['row_total'], (string) $cartItem['quantity'], 4);

            if ($cheapestUnitPrice === null || bccomp($unitPrice, $cheapestUnitPrice, 4) < 0) {
                $cheapestUnitPrice = $unitPrice;
                $cheapestRowTotal = $unitPrice;
            }
        }

        return $cheapestRowTotal ?? '0.0000';
    }
}
