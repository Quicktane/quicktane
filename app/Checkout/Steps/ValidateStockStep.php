<?php

declare(strict_types=1);

namespace App\Checkout\Steps;

use App\Cart\Contracts\CartFacade;
use Closure;
use Illuminate\Validation\ValidationException;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;
use Quicktane\Inventory\Contracts\StockFacade;

class ValidateStockStep implements PipelineStep
{
    public function __construct(
        private readonly CartFacade $cartFacade,
        private readonly StockFacade $stockFacade,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $cartId = (int) $context->get('cart_id');
        $cart = $this->cartFacade->getCartWithItems($cartId);

        $insufficientItems = [];

        foreach ($cart->items as $item) {
            $salableQuantity = $this->stockFacade->getSalableQuantity($item->product_id);

            if ($salableQuantity < $item->quantity) {
                $insufficientItems[] = "{$item->name} (available: {$salableQuantity}, requested: {$item->quantity})";
            }
        }

        if (! empty($insufficientItems)) {
            throw ValidationException::withMessages([
                'stock' => ['Insufficient stock for: '.implode(', ', $insufficientItems)],
            ]);
        }

        return $next($context);
    }

    public function compensate(PipelineContext $context): void {}

    public static function priority(): int
    {
        return 800;
    }

    public static function pipeline(): string
    {
        return 'checkout.place';
    }
}
