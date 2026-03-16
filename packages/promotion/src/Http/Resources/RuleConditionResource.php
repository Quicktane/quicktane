<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RuleConditionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'attribute' => $this->attribute,
            'operator' => $this->operator?->value,
            'value' => $this->value,
            'aggregator' => $this->aggregator?->value,
            'is_inverted' => $this->is_inverted,
            'sort_order' => $this->sort_order,
            'children' => self::collection($this->whenLoaded('children')),
        ];
    }
}
