<?php

declare(strict_types=1);

namespace App\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditMemoResource extends JsonResource
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
            'adjustment_positive' => $this->adjustment_positive,
            'adjustment_negative' => $this->adjustment_negative,
            'tax_amount' => $this->tax_amount,
            'grand_total' => $this->grand_total,
            'items' => CreditMemoItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
        ];
    }
}
