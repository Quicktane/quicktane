<?php

declare(strict_types=1);

namespace App\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderAddressResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company' => $this->company,
            'street_line_1' => $this->street_line_1,
            'street_line_2' => $this->street_line_2,
            'city' => $this->city,
            'region_id' => $this->region_id,
            'region_name' => $this->region_name,
            'postcode' => $this->postcode,
            'country_id' => $this->country_id,
            'country_name' => $this->country_name,
            'phone' => $this->phone,
        ];
    }
}
