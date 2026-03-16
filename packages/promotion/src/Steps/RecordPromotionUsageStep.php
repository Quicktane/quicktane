<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Steps;

use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;
use Quicktane\Promotion\Contracts\PromotionFacade;
use Quicktane\Promotion\DataTransferObjects\DiscountResult;
use Quicktane\Promotion\Repositories\CartPriceRuleRepository;
use Quicktane\Promotion\Repositories\CouponRepository;
use Quicktane\Promotion\Repositories\CouponUsageRepository;

class RecordPromotionUsageStep implements PipelineStep
{
    public function __construct(
        private readonly PromotionFacade $promotionFacade,
        private readonly CartPriceRuleRepository $cartPriceRuleRepository,
        private readonly CouponRepository $couponRepository,
        private readonly CouponUsageRepository $couponUsageRepository,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $orderId = (int) $context->get('order_id');
        $customerId = $context->get('customer_id');
        $couponCode = $context->get('coupon_code');

        /** @var DiscountResult[] $discountDetails */
        $discountDetails = $context->get('discount_details', []);

        $recordedUsages = [];

        foreach ($discountDetails as $discountResult) {
            $couponId = null;

            if ($couponCode !== null) {
                $coupon = $this->couponRepository->findByCode($couponCode);
                $couponId = $coupon?->id;
            }

            $this->promotionFacade->recordUsage(
                $discountResult->ruleId,
                $couponId,
                $customerId,
                $orderId,
                $discountResult->discountAmount,
            );

            $recordedUsages[] = [
                'rule_id' => $discountResult->ruleId,
                'coupon_id' => $couponId,
                'discount_amount' => $discountResult->discountAmount,
            ];
        }

        $context->set('recorded_promotion_usages', $recordedUsages);

        return $next($context);
    }

    public function compensate(PipelineContext $context): void
    {
        /** @var array<int, array{rule_id: int, coupon_id: int|null, discount_amount: string}> $recordedUsages */
        $recordedUsages = $context->get('recorded_promotion_usages', []);

        foreach ($recordedUsages as $recordedUsage) {
            $cartPriceRule = $this->cartPriceRuleRepository->findById($recordedUsage['rule_id']);

            if ($cartPriceRule !== null && $cartPriceRule->times_used > 0) {
                $this->cartPriceRuleRepository->update($cartPriceRule, [
                    'times_used' => $cartPriceRule->times_used - 1,
                ]);
            }

            if ($recordedUsage['coupon_id'] !== null) {
                $coupon = $this->couponRepository->findById($recordedUsage['coupon_id']);

                if ($coupon !== null && $coupon->times_used > 0) {
                    $this->couponRepository->update($coupon, [
                        'times_used' => $coupon->times_used - 1,
                    ]);
                }
            }
        }
    }

    public static function priority(): int
    {
        return 300;
    }

    public static function pipeline(): string
    {
        return 'checkout.place';
    }
}
