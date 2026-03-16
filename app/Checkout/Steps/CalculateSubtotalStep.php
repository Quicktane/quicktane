<?php

declare(strict_types=1);

namespace App\Checkout\Steps;

use App\Cart\Contracts\CartFacade;
use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;

class CalculateSubtotalStep implements PipelineStep
{
    public function __construct(
        private readonly CartFacade $cartFacade,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $cartId = (int) $context->get('cart_id');
        $cartTotals = $this->cartFacade->getCartTotals($cartId);

        $context->set('subtotal', $cartTotals->subtotal);
        $context->set('items_count', $cartTotals->itemsCount);
        $context->set('cart_items', $cartTotals->items);

        return $next($context);
    }

    public function compensate(PipelineContext $context): void {}

    public static function priority(): int
    {
        return 1000;
    }

    public static function pipeline(): string
    {
        return 'checkout.totals';
    }
}
