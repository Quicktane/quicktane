<?php

declare(strict_types=1);

namespace App\Directory\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'iso2' => $this->iso2,
            'iso3' => $this->iso3,
            'name' => $this->name,
            'numeric_code' => $this->numeric_code,
            'phone_code' => $this->phone_code,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'regions' => RegionResource::collection($this->whenLoaded('regions')),
        ];
    }
}
