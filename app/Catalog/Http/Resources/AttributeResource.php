<?php

declare(strict_types=1);

namespace App\Catalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'is_required' => $this->is_required,
            'is_filterable' => $this->is_filterable,
            'is_visible' => $this->is_visible,
            'sort_order' => $this->sort_order,
            'validation_rules' => $this->validation_rules,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'options' => AttributeOptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
