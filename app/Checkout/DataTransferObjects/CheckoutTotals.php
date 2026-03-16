<?php

declare(strict_types=1);

namespace App\Checkout\DataTransferObjects;

class CheckoutTotals
{
    /**
     * @param  array<string, mixed>  $breakdown
     */
    public function __construct(
        public readonly string $subtotal,
        public readonly string $shippingAmount,
        public readonly string $discountAmount,
        public readonly string $taxAmount,
        public readonly string $grandTotal,
        public readonly array $breakdown,
        public readonly bool $freeShipping,
    ) {}
}
