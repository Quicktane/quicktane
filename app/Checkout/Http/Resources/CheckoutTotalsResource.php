<?php

declare(strict_types=1);

namespace App\Checkout\Http\Resources;

use App\Checkout\DataTransferObjects\CheckoutTotals;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutTotalsResource extends JsonResource
{
    public function __construct(
        private readonly CheckoutTotals $checkoutTotals,
    ) {
        parent::__construct($checkoutTotals);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'subtotal' => $this->checkoutTotals->subtotal,
            'shipping_amount' => $this->checkoutTotals->shippingAmount,
            'discount_amount' => $this->checkoutTotals->discountAmount,
            'tax_amount' => $this->checkoutTotals->taxAmount,
            'grand_total' => $this->checkoutTotals->grandTotal,
            'breakdown' => $this->checkoutTotals->breakdown,
            'free_shipping' => $this->checkoutTotals->freeShipping,
        ];
    }
}
