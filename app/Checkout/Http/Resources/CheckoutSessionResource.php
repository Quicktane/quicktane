<?php

declare(strict_types=1);

namespace App\Checkout\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutSessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'cart_id' => $this->cart_id,
            'customer_id' => $this->customer_id,
            'shipping_address' => $this->shipping_address,
            'billing_address' => $this->billing_address,
            'shipping_method_code' => $this->shipping_method_code,
            'shipping_method_label' => $this->shipping_method_label,
            'shipping_amount' => $this->shipping_amount,
            'payment_method_code' => $this->payment_method_code,
            'coupon_code' => $this->coupon_code,
            'totals' => $this->totals,
            'step' => $this->step,
            'expires_at' => $this->expires_at,
        ];
    }
}
