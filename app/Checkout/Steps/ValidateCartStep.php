<?php

declare(strict_types=1);

namespace App\Checkout\Steps;

use App\Cart\Contracts\CartFacade;
use App\Cart\Models\CartStatus;
use Closure;
use Illuminate\Validation\ValidationException;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;

class ValidateCartStep implements PipelineStep
{
    public function __construct(
        private readonly CartFacade $cartFacade,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $cartId = (int) $context->get('cart_id');
        $cart = $this->cartFacade->getCartWithItems($cartId);

        if ($cart === null) {
            throw ValidationException::withMessages([
                'cart' => ['Cart not found.'],
            ]);
        }

        if ($cart->status !== CartStatus::Active) {
            throw ValidationException::withMessages([
                'cart' => ['Cart is not active.'],
            ]);
        }

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => ['Cart is empty.'],
            ]);
        }

        $context->set('currency_code', $cart->currency_code);
        $context->set('store_id', $cart->store_id);

        $totalQuantity = 0;

        foreach ($cart->items as $item) {
            $totalQuantity += $item->quantity;
        }

        $context->set('total_quantity', $totalQuantity);

        return $next($context);
    }

    public function compensate(PipelineContext $context): void {}

    public static function priority(): int
    {
        return 1000;
    }

    public static function pipeline(): string
    {
        return 'checkout.place';
    }
}
