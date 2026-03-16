<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxRuleResource extends JsonResource
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
            'tax_rate_id' => $this->tax_rate_id,
            'tax_rate' => new TaxRateResource($this->whenLoaded('taxRate')),
            'product_tax_class_id' => $this->product_tax_class_id,
            'product_tax_class' => new TaxClassResource($this->whenLoaded('productTaxClass')),
            'customer_tax_class_id' => $this->customer_tax_class_id,
            'customer_tax_class' => new TaxClassResource($this->whenLoaded('customerTaxClass')),
            'priority' => $this->priority,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
