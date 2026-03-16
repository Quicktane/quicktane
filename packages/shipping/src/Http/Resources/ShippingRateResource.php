<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'shipping_method_id' => $this->shipping_method_id,
            'shipping_zone_id' => $this->shipping_zone_id,
            'price' => $this->price,
            'min_weight' => $this->min_weight,
            'max_weight' => $this->max_weight,
            'min_subtotal' => $this->min_subtotal,
            'max_subtotal' => $this->max_subtotal,
            'is_active' => $this->is_active,
            'method' => new ShippingMethodResource($this->whenLoaded('method')),
            'zone' => new ShippingZoneResource($this->whenLoaded('zone')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
