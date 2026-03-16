<?php

declare(strict_types=1);

namespace App\Catalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'type' => $this->type,
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'base_price' => $this->base_price,
            'special_price' => $this->special_price,
            'special_price_from' => $this->special_price_from,
            'special_price_to' => $this->special_price_to,
            'cost' => $this->cost,
            'weight' => $this->weight,
            'is_active' => $this->is_active,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'resolved_price' => $this->when(isset($this->resolved_price), $this->resolved_price),
            'is_on_sale' => $this->when(isset($this->is_on_sale), $this->is_on_sale),
            'attribute_set' => new AttributeSetResource($this->whenLoaded('attributeSet')),
            'attribute_values' => AttributeValueResource::collection($this->whenLoaded('attributeValues')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'media' => ProductMediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
