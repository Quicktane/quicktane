<?php

declare(strict_types=1);

namespace App\Cart\Facades;

use App\Cart\Contracts\CartFacade as CartFacadeContract;
use App\Cart\DataTransferObjects\CartTotals;
use App\Cart\DataTransferObjects\PriceValidationResult;
use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;
use App\Cart\Services\CartService;
use App\Cart\Services\PriceValidationService;

class CartFacade implements CartFacadeContract
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly PriceValidationService $priceValidationService,
    ) {}

    public function getActiveCart(int $customerId): ?Cart
    {
        return $this->cartService->getActiveCart($customerId);
    }

    public function getActiveGuestCart(string $guestToken): ?Cart
    {
        return $this->cartService->getActiveGuestCart($guestToken);
    }

    public function getCartWithItems(int $cartId): ?Cart
    {
        return $this->cartService->getCartWithItems($cartId);
    }

    public function addItem(int $cartId, string $productUuid, int $quantity, ?array $options = null): CartItem
    {
        return $this->cartService->addItem($cartId, $productUuid, $quantity, $options);
    }

    public function updateItemQuantity(string $cartItemUuid, int $quantity): CartItem
    {
        return $this->cartService->updateItemQuantity($cartItemUuid, $quantity);
    }

    public function removeItem(string $cartItemUuid): void
    {
        $this->cartService->removeItem($cartItemUuid);
    }

    public function clearCart(int $cartId): void
    {
        $this->cartService->clearCart($cartId);
    }

    public function getCartTotals(int $cartId): CartTotals
    {
        return $this->cartService->getCartTotals($cartId);
    }

    public function revalidatePrices(int $cartId): PriceValidationResult
    {
        return $this->priceValidationService->validate($cartId);
    }

    public function confirmPriceChanges(int $cartId): void
    {
        $this->priceValidationService->confirmChanges($cartId);
    }

    public function mergeGuestCart(string $guestToken, int $customerId): Cart
    {
        return $this->cartService->mergeGuestCart($guestToken, $customerId);
    }
}
