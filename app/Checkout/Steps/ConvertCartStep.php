<?php

declare(strict_types=1);

namespace App\Checkout\Steps;

use App\Cart\Models\CartStatus;
use App\Cart\Repositories\CartRepository;
use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;

class ConvertCartStep implements PipelineStep
{
    public function __construct(
        private readonly CartRepository $cartRepository,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $cartId = (int) $context->get('cart_id');
        $cart = $this->cartRepository->findById($cartId);

        if ($cart !== null) {
            $this->cartRepository->update($cart, [
                'status' => CartStatus::Converted,
                'converted_at' => now(),
            ]);
        }

        return $next($context);
    }

    public function compensate(PipelineContext $context): void
    {
        $cartId = $context->get('cart_id');

        if ($cartId !== null) {
            $cart = $this->cartRepository->findById((int) $cartId);

            if ($cart !== null) {
                $this->cartRepository->update($cart, [
                    'status' => CartStatus::Active,
                    'converted_at' => null,
                ]);
            }
        }
    }

    public static function priority(): int
    {
        return 400;
    }

    public static function pipeline(): string
    {
        return 'checkout.place';
    }
}
