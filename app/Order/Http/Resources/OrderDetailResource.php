<?php

declare(strict_types=1);

namespace App\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'customer_group_id' => $this->customer_group_id,
            'status' => $this->status->value,
            'subtotal' => $this->subtotal,
            'shipping_amount' => $this->shipping_amount,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'grand_total' => $this->grand_total,
            'total_paid' => $this->total_paid,
            'total_refunded' => $this->total_refunded,
            'currency_code' => $this->currency_code,
            'shipping_method_code' => $this->shipping_method_code,
            'shipping_method_label' => $this->shipping_method_label,
            'payment_method_code' => $this->payment_method_code,
            'payment_method_label' => $this->payment_method_label,
            'coupon_code' => $this->coupon_code,
            'total_quantity' => $this->total_quantity,
            'weight' => $this->weight,
            'customer_note' => $this->customer_note,
            'admin_note' => $this->admin_note,
            'ip_address' => $this->ip_address,
            'customer' => $this->when($this->relationLoaded('customer') && $this->customer !== null, fn () => [
                'uuid' => $this->customer->uuid,
                'email' => $this->customer->email,
                'name' => $this->customer->first_name.' '.$this->customer->last_name,
            ]),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'addresses' => OrderAddressResource::collection($this->whenLoaded('addresses')),
            'history' => OrderHistoryResource::collection($this->whenLoaded('history')),
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
            'credit_memos' => CreditMemoResource::collection($this->whenLoaded('creditMemos')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
