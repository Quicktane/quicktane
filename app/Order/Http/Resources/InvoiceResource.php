<?php

declare(strict_types=1);

namespace App\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'status' => $this->status->value,
            'subtotal' => $this->subtotal,
            'shipping_amount' => $this->shipping_amount,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'grand_total' => $this->grand_total,
            'order_increment_id' => $this->when($this->relationLoaded('order') && $this->order !== null, fn () => $this->order->increment_id),
            'order_uuid' => $this->when($this->relationLoaded('order') && $this->order !== null, fn () => $this->order->uuid),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
        ];
    }
}
