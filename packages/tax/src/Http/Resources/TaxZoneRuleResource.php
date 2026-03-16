<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxZoneRuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'region_id' => $this->region_id,
            'postcode_from' => $this->postcode_from,
            'postcode_to' => $this->postcode_to,
        ];
    }
}
