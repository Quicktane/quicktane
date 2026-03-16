<?php

declare(strict_types=1);

namespace App\Checkout\Steps;

use App\Cart\Contracts\CartFacade;
use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;
use Quicktane\Inventory\Contracts\ReservationFacade;

class ReserveInventoryStep implements PipelineStep
{
    public function __construct(
        private readonly CartFacade $cartFacade,
        private readonly ReservationFacade $reservationFacade,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $cartId = (int) $context->get('cart_id');
        $cart = $this->cartFacade->getCartWithItems($cartId);

        $reservations = [];

        foreach ($cart->items as $item) {
            $this->reservationFacade->reserve($item->product_id, 1, $item->quantity);
            $reservations[] = [
                'product_id' => $item->product_id,
                'source_id' => 1,
                'quantity' => $item->quantity,
            ];
        }

        $context->set('reservations', $reservations);

        return $next($context);
    }

    public function compensate(PipelineContext $context): void
    {
        $reservations = (array) $context->get('reservations', []);

        foreach ($reservations as $reservation) {
            $this->reservationFacade->release(
                (int) $reservation['product_id'],
                (int) $reservation['source_id'],
                (int) $reservation['quantity'],
            );
        }
    }

    public static function priority(): int
    {
        return 600;
    }

    public static function pipeline(): string
    {
        return 'checkout.place';
    }
}
