<?php

declare(strict_types=1);

namespace App\Checkout\Steps;

use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;

class CalculateGrandTotalStep implements PipelineStep
{
    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $subtotal = (string) $context->get('subtotal', '0.0000');
        $shippingAmount = (string) $context->get('shipping_amount', '0.0000');
        $discountAmount = (string) $context->get('discount_amount', '0.0000');
        $taxAmount = (string) $context->get('tax_amount', '0.0000');

        $grandTotal = bcadd($subtotal, $shippingAmount, 4);
        $grandTotal = bcsub($grandTotal, $discountAmount, 4);
        $grandTotal = bcadd($grandTotal, $taxAmount, 4);

        if (bccomp($grandTotal, '0', 4) < 0) {
            $grandTotal = '0.0000';
        }

        $context->set('grand_total', $grandTotal);

        return $next($context);
    }

    public function compensate(PipelineContext $context): void {}

    public static function priority(): int
    {
        return 200;
    }

    public static function pipeline(): string
    {
        return 'checkout.totals';
    }
}
