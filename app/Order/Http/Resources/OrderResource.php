<?php

declare(strict_types=1);

namespace App\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'increment_id' => $this->increment_id,
            'store_id' => $this->store_id,
            'customer_id' => $this->customer_id,
            'customer_email' => $this->customer_email,
            'status' => $this->status->value,
            'subtotal' => $this->subtotal,
            'shipping_amount' => $this->shipping_amount,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'grand_total' => $this->grand_total,
            'total_paid' => $this->total_paid,
            'total_refunded' => $this->total_refunded,
            'currency_code' => $this->currency_code,
            'shipping_method_label' => $this->shipping_method_label,
            'payment_method_label' => $this->payment_method_label,
            'coupon_code' => $this->coupon_code,
            'total_quantity' => $this->total_quantity,
            'customer' => $this->when($this->relationLoaded('customer') && $this->customer !== null, fn () => [
                'uuid' => $this->customer->uuid,
                'email' => $this->customer->email,
                'name' => $this->customer->first_name.' '.$this->customer->last_name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
