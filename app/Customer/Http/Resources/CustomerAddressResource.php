<?php

declare(strict_types=1);

namespace App\Customer\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company' => $this->company,
            'street_line_1' => $this->street_line_1,
            'street_line_2' => $this->street_line_2,
            'city' => $this->city,
            'region_id' => $this->region_id,
            'postcode' => $this->postcode,
            'country_id' => $this->country_id,
            'phone' => $this->phone,
            'is_default_billing' => $this->is_default_billing,
            'is_default_shipping' => $this->is_default_shipping,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
