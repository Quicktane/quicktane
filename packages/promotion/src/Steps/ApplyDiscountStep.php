<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Steps;

use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;
use Quicktane\Promotion\Contracts\PromotionFacade;

class ApplyDiscountStep implements PipelineStep
{
    public function __construct(
        private readonly PromotionFacade $promotionFacade,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $cartId = (int) $context->get('cart_id');
        $customerId = $context->get('customer_id');
        $couponCode = $context->get('coupon_code');

        $promotionResult = $this->promotionFacade->applyRules($cartId, $customerId, $couponCode);

        $context->set('discount_amount', $promotionResult->totalDiscount);
        $context->set('discount_details', $promotionResult->discounts);
        $context->set('free_shipping', $promotionResult->freeShipping);

        return $next($context);
    }

    public function compensate(PipelineContext $context): void
    {
        // No-op: discount calculation is stateless
    }

    public static function priority(): int
    {
        return 600;
    }

    public static function pipeline(): string
    {
        return 'checkout.totals';
    }
}
