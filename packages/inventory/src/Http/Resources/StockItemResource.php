<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'source_id' => $this->source_id,
            'quantity' => $this->quantity,
            'reserved' => $this->reserved,
            'notify_quantity' => $this->notify_quantity,
            'is_in_stock' => $this->is_in_stock,
            'salable_quantity' => $this->quantity - $this->reserved,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'source' => new InventorySourceResource($this->whenLoaded('source')),
            'product' => $this->when($this->relationLoaded('product'), function () {
                return [
                    'uuid' => $this->product->uuid,
                    'sku' => $this->product->sku,
                    'name' => $this->product->name,
                ];
            }),
        ];
    }
}
