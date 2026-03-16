<?php

declare(strict_types=1);

namespace Quicktane\Tax\Steps;

use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;
use Quicktane\Tax\Contracts\TaxFacade;

class CalculateTaxStep implements PipelineStep
{
    public function __construct(
        private readonly TaxFacade $taxFacade,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $cartId = (int) $context->get('cart_id');
        $shippingAddress = (array) $context->get('shipping_address', []);
        $customerTaxClassId = $context->get('customer_tax_class_id');

        $cartTaxResult = $this->taxFacade->calculateCartTax(
            $cartId,
            $shippingAddress,
            $customerTaxClassId !== null ? (int) $customerTaxClassId : null,
        );

        $context->set('tax_amount', $cartTaxResult->totalTax);
        $context->set('tax_details', $cartTaxResult->itemTaxes);

        return $next($context);
    }

    public function compensate(PipelineContext $context): void {}

    public static function priority(): int
    {
        return 400;
    }

    public static function pipeline(): string
    {
        return 'checkout.totals';
    }
}
