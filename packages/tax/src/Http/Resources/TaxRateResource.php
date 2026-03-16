<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxRateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'tax_zone_id' => $this->tax_zone_id,
            'zone' => new TaxZoneResource($this->whenLoaded('zone')),
            'rate' => $this->rate,
            'priority' => $this->priority,
            'is_compound' => $this->is_compound,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
