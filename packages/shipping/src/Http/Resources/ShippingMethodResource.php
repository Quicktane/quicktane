<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'code' => $this->code,
            'name' => $this->name,
            'carrier_code' => $this->carrier_code,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'min_order_amount' => $this->min_order_amount,
            'max_order_amount' => $this->max_order_amount,
            'free_shipping_threshold' => $this->free_shipping_threshold,
            'config' => $this->config,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
