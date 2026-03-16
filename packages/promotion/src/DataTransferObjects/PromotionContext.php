<?php

declare(strict_types=1);

namespace Quicktane\Promotion\DataTransferObjects;

class PromotionContext
{
    /**
     * @param  array<int, array{product_id: int, sku: string, category_ids: int[], quantity: int, row_total: string, product_type: string}>  $cartItems
     */
    public function __construct(
        public readonly array $cartItems,
        public readonly string $subtotal,
        public readonly int $itemsCount,
        public readonly ?string $totalWeight,
        public readonly ?int $customerId,
        public readonly ?int $customerGroupId,
        public readonly ?string $couponCode,
    ) {}
}
