<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Steps;

use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;
use Quicktane\Shipping\DataTransferObjects\ShippingRateRequest;
use Quicktane\Shipping\Services\ShippingRateService;

class CalculateShippingStep implements PipelineStep
{
    public function __construct(
        private readonly ShippingRateService $shippingRateService,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $shippingMethodCode = $context->get('shipping_method_code');

        if ($shippingMethodCode === null) {
            return $next($context);
        }

        $carrierCode = $context->get('shipping_carrier_code', 'flat_rate');

        $shippingRateRequest = new ShippingRateRequest(
            items: $context->get('items', []),
            shippingAddress: $context->get('shipping_address', []),
            subtotal: (string) $context->get('subtotal', '0.0000'),
            totalWeight: $context->get('total_weight'),
            currencyCode: (string) $context->get('currency_code', 'USD'),
        );

        $rateOption = $this->shippingRateService->resolveRate(
            $carrierCode,
            $shippingMethodCode,
            $shippingRateRequest,
        );

        if ($rateOption !== null) {
            $context->set('shipping_amount', $rateOption->price);
            $context->set('shipping_carrier_code', $rateOption->carrierCode);
            $context->set('shipping_method_code', $rateOption->methodCode);
            $context->set('shipping_label', $rateOption->label);
        }

        return $next($context);
    }

    public function compensate(PipelineContext $context): void
    {
        // No-op
    }

    public static function priority(): int
    {
        return 800;
    }

    public static function pipeline(): string
    {
        return 'checkout.totals';
    }
}
