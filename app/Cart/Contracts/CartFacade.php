<?php

declare(strict_types=1);

namespace App\Cart\Contracts;

use App\Cart\DataTransferObjects\CartTotals;
use App\Cart\DataTransferObjects\PriceValidationResult;
use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;

interface CartFacade
{
    public function getActiveCart(int $customerId): ?Cart;

    public function getActiveGuestCart(string $guestToken): ?Cart;

    public function getCartWithItems(int $cartId): ?Cart;

    public function addItem(int $cartId, string $productUuid, int $quantity, ?array $options = null): CartItem;

    public function updateItemQuantity(string $cartItemUuid, int $quantity): CartItem;

    public function removeItem(string $cartItemUuid): void;

    public function clearCart(int $cartId): void;

    public function getCartTotals(int $cartId): CartTotals;

    public function revalidatePrices(int $cartId): PriceValidationResult;

    public function confirmPriceChanges(int $cartId): void;

    public function mergeGuestCart(string $guestToken, int $customerId): Cart;
}
