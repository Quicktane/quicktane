<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\DataTransferObjects\CartTotals;
use App\Cart\Events\AfterCartItemAdd;
use App\Cart\Events\AfterCartItemRemove;
use App\Cart\Events\AfterCartMerge;
use App\Cart\Events\BeforeCartItemAdd;
use App\Cart\Events\BeforeCartItemRemove;
use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;
use App\Cart\Models\CartStatus;
use App\Cart\Repositories\CartItemRepository;
use App\Cart\Repositories\CartRepository;
use App\Catalog\Contracts\PricingFacade;
use App\Catalog\Contracts\ProductFacade;
use Illuminate\Validation\ValidationException;
use Quicktane\Core\Events\EventDispatcher;
use Quicktane\Core\Events\OperationContext;
use Quicktane\Core\Trace\OperationTracer;
use Quicktane\Inventory\Contracts\StockFacade;

class CartService
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly CartItemRepository $cartItemRepository,
        private readonly ProductFacade $productFacade,
        private readonly PricingFacade $pricingFacade,
        private readonly StockFacade $stockFacade,
        private readonly OperationTracer $operationTracer,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    public function getActiveCart(int $customerId): ?Cart
    {
        return $this->cartRepository->findActiveByCustomer($customerId);
    }

    public function getActiveGuestCart(string $guestToken): ?Cart
    {
        return $this->cartRepository->findActiveByGuestToken($guestToken);
    }

    public function getCartWithItems(int $cartId): ?Cart
    {
        $cart = $this->cartRepository->findById($cartId);

        if ($cart !== null) {
            $cart->load('items');
        }

        return $cart;
    }

    public function getOrCreateCart(int $customerId, int $storeId, string $currencyCode): Cart
    {
        $cart = $this->cartRepository->findActiveByCustomer($customerId);

        if ($cart !== null) {
            return $cart;
        }

        return $this->cartRepository->create([
            'customer_id' => $customerId,
            'store_id' => $storeId,
            'currency_code' => $currencyCode,
            'status' => CartStatus::Active,
        ]);
    }

    public function getOrCreateGuestCart(string $guestToken, int $storeId, string $currencyCode): Cart
    {
        $cart = $this->cartRepository->findActiveByGuestToken($guestToken);

        if ($cart !== null) {
            return $cart;
        }

        return $this->cartRepository->create([
            'guest_token' => $guestToken,
            'store_id' => $storeId,
            'currency_code' => $currencyCode,
            'status' => CartStatus::Active,
        ]);
    }

    public function addItem(int $cartId, string $productUuid, int $quantity, ?array $options = null): CartItem
    {
        return $this->operationTracer->execute('cart.item.add', function () use ($cartId, $productUuid, $quantity, $options): CartItem {
            $product = $this->productFacade->getProduct($productUuid);

            if ($product === null || ! $product->is_active) {
                throw ValidationException::withMessages([
                    'product_uuid' => ['Product not found or is inactive.'],
                ]);
            }

            $salableQuantity = $this->stockFacade->getSalableQuantity($product->id);

            $existingItem = $this->cartItemRepository->findByCartAndProduct($cartId, $product->id, $options);
            $totalQuantity = $existingItem !== null ? $existingItem->quantity + $quantity : $quantity;

            if ($salableQuantity < $totalQuantity) {
                throw ValidationException::withMessages([
                    'quantity' => ["Insufficient stock. Available: {$salableQuantity}."],
                ]);
            }

            $price = $this->pricingFacade->resolvePrice($product);

            $context = new OperationContext;
            $context->set('cart_id', $cartId);
            $context->set('product_id', $product->id);
            $context->set('quantity', $quantity);
            $context->set('price', $price);

            $this->eventDispatcher->dispatch(new BeforeCartItemAdd($context));

            if ($existingItem !== null) {
                $newQuantity = $existingItem->quantity + $quantity;
                $cartItem = $this->cartItemRepository->update($existingItem, [
                    'quantity' => $newQuantity,
                    'unit_price' => $price,
                    'row_total' => bcmul($price, (string) $newQuantity, 4),
                    'snapshotted_at' => now(),
                ]);
            } else {
                $cartItem = $this->cartItemRepository->create([
                    'cart_id' => $cartId,
                    'product_id' => $product->id,
                    'product_uuid' => $product->uuid,
                    'product_type' => $product->type->value,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'row_total' => bcmul($price, (string) $quantity, 4),
                    'options' => $options,
                    'snapshotted_at' => now(),
                ]);
            }

            $this->recalculateSubtotal($cartId);

            $this->eventDispatcher->dispatch(new AfterCartItemAdd($cartItem, $context));

            return $cartItem;
        });
    }

    public function updateItemQuantity(string $cartItemUuid, int $quantity): CartItem
    {
        return $this->operationTracer->execute('cart.item.update', function () use ($cartItemUuid, $quantity): CartItem {
            $cartItem = $this->cartItemRepository->findByUuid($cartItemUuid);

            if ($cartItem === null) {
                throw ValidationException::withMessages([
                    'cart_item' => ['Cart item not found.'],
                ]);
            }

            $salableQuantity = $this->stockFacade->getSalableQuantity($cartItem->product_id);

            if ($salableQuantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => ["Insufficient stock. Available: {$salableQuantity}."],
                ]);
            }

            $cartItem = $this->cartItemRepository->update($cartItem, [
                'quantity' => $quantity,
                'row_total' => bcmul($cartItem->unit_price, (string) $quantity, 4),
            ]);

            $this->recalculateSubtotal($cartItem->cart_id);

            return $cartItem;
        });
    }

    public function removeItem(string $cartItemUuid): void
    {
        $this->operationTracer->execute('cart.item.remove', function () use ($cartItemUuid): void {
            $cartItem = $this->cartItemRepository->findByUuid($cartItemUuid);

            if ($cartItem === null) {
                throw ValidationException::withMessages([
                    'cart_item' => ['Cart item not found.'],
                ]);
            }

            $context = new OperationContext;
            $context->set('cart_id', $cartItem->cart_id);
            $context->set('product_id', $cartItem->product_id);

            $this->eventDispatcher->dispatch(new BeforeCartItemRemove($cartItem, $context));

            $cartId = $cartItem->cart_id;
            $this->cartItemRepository->delete($cartItem);
            $this->recalculateSubtotal($cartId);

            $this->eventDispatcher->dispatch(new AfterCartItemRemove($cartItem, $context));
        });
    }

    public function clearCart(int $cartId): void
    {
        $this->cartItemRepository->deleteByCart($cartId);

        $this->cartRepository->update(
            $this->cartRepository->findById($cartId),
            ['items_count' => 0, 'subtotal' => '0.0000'],
        );
    }

    public function getCartTotals(int $cartId): CartTotals
    {
        $cart = $this->cartRepository->findById($cartId);
        $items = $this->cartItemRepository->getByCart($cartId);

        return new CartTotals(
            subtotal: $cart->subtotal,
            itemsCount: $cart->items_count,
            items: $items->map(fn (CartItem $item): array => [
                'uuid' => $item->uuid,
                'sku' => $item->sku,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'row_total' => $item->row_total,
            ])->all(),
        );
    }

    public function mergeGuestCart(string $guestToken, int $customerId): Cart
    {
        $guestCart = $this->cartRepository->findActiveByGuestToken($guestToken);

        if ($guestCart === null) {
            return $this->cartRepository->findActiveByCustomer($customerId)
                ?? $this->cartRepository->create([
                    'customer_id' => $customerId,
                    'store_id' => 1,
                    'currency_code' => 'USD',
                    'status' => CartStatus::Active,
                ]);
        }

        $customerCart = $this->cartRepository->findActiveByCustomer($customerId);

        if ($customerCart === null) {
            $this->cartRepository->update($guestCart, [
                'customer_id' => $customerId,
                'guest_token' => null,
            ]);

            return $guestCart->fresh();
        }

        $guestItems = $this->cartItemRepository->getByCart($guestCart->id);

        foreach ($guestItems as $guestItem) {
            $existingItem = $this->cartItemRepository->findByCartAndProduct(
                $customerCart->id,
                $guestItem->product_id,
                $guestItem->options,
            );

            if ($existingItem === null) {
                $this->cartItemRepository->create([
                    'cart_id' => $customerCart->id,
                    'product_id' => $guestItem->product_id,
                    'product_uuid' => $guestItem->product_uuid,
                    'product_type' => $guestItem->product_type,
                    'sku' => $guestItem->sku,
                    'name' => $guestItem->name,
                    'quantity' => $guestItem->quantity,
                    'unit_price' => $guestItem->unit_price,
                    'row_total' => $guestItem->row_total,
                    'options' => $guestItem->options,
                    'snapshotted_at' => $guestItem->snapshotted_at,
                ]);
            }
        }

        $this->cartRepository->update($guestCart, ['status' => CartStatus::Merged]);
        $this->recalculateSubtotal($customerCart->id);

        $this->eventDispatcher->dispatch(new AfterCartMerge($customerCart, $guestCart));

        return $customerCart->fresh();
    }

    private function recalculateSubtotal(int $cartId): void
    {
        $items = $this->cartItemRepository->getByCart($cartId);

        $subtotal = '0.0000';
        foreach ($items as $item) {
            $subtotal = bcadd($subtotal, $item->row_total, 4);
        }

        $cart = $this->cartRepository->findById($cartId);
        $this->cartRepository->update($cart, [
            'subtotal' => $subtotal,
            'items_count' => $items->count(),
        ]);
    }
}
