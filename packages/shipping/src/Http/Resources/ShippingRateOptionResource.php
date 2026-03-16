<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRateOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'carrier_code' => $this->carrierCode,
            'method_code' => $this->methodCode,
            'label' => $this->label,
            'price' => $this->price,
            'estimated_days' => $this->estimatedDays,
        ];
    }
}
