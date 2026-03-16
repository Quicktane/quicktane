<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'countries' => $this->whenLoaded('countries', function () {
                return $this->countries->map(function ($shippingZoneCountry) {
                    return [
                        'id' => $shippingZoneCountry->id,
                        'country_id' => $shippingZoneCountry->country_id,
                        'region_id' => $shippingZoneCountry->region_id,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
