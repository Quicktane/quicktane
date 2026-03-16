<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\DataTransferObjects\PriceValidationResult;
use App\Cart\Repositories\CartItemRepository;
use App\Cart\Repositories\CartRepository;
use App\Catalog\Contracts\PricingFacade;
use App\Catalog\Contracts\ProductFacade;
use Quicktane\Inventory\Contracts\StockFacade;

class PriceValidationService
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly CartItemRepository $cartItemRepository,
        private readonly ProductFacade $productFacade,
        private readonly PricingFacade $pricingFacade,
        private readonly StockFacade $stockFacade,
    ) {}

    public function validate(int $cartId): PriceValidationResult
    {
        $items = $this->cartItemRepository->getByCart($cartId);

        $changedItems = [];
        $outOfStockItems = [];
        $insufficientStockItems = [];

        foreach ($items as $item) {
            $product = $this->productFacade->getProduct($item->product_uuid);

            if ($product === null || ! $product->is_active) {
                $outOfStockItems[] = $item->uuid;

                continue;
            }

            $salableQuantity = $this->stockFacade->getSalableQuantity($item->product_id);

            if ($salableQuantity === 0) {
                $outOfStockItems[] = $item->uuid;

                continue;
            }

            if ($salableQuantity < $item->quantity) {
                $insufficientStockItems[] = [
                    'item_uuid' => $item->uuid,
                    'requested' => $item->quantity,
                    'available' => $salableQuantity,
                ];
            }

            $currentPrice = $this->pricingFacade->resolvePrice($product);

            if (bccomp($currentPrice, $item->unit_price, 4) !== 0) {
                $changedItems[] = [
                    'item_uuid' => $item->uuid,
                    'old_price' => $item->unit_price,
                    'new_price' => $currentPrice,
                ];
            }
        }

        $isValid = empty($changedItems) && empty($outOfStockItems) && empty($insufficientStockItems);

        return new PriceValidationResult(
            isValid: $isValid,
            changedItems: $changedItems,
            outOfStockItems: $outOfStockItems,
            insufficientStockItems: $insufficientStockItems,
        );
    }

    public function confirmChanges(int $cartId): void
    {
        $items = $this->cartItemRepository->getByCart($cartId);

        foreach ($items as $item) {
            $product = $this->productFacade->getProduct($item->product_uuid);

            if ($product === null || ! $product->is_active) {
                continue;
            }

            $currentPrice = $this->pricingFacade->resolvePrice($product);

            $this->cartItemRepository->update($item, [
                'unit_price' => $currentPrice,
                'row_total' => bcmul($currentPrice, (string) $item->quantity, 4),
                'snapshotted_at' => now(),
            ]);
        }

        $this->recalculateSubtotal($cartId);
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
