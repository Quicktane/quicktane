<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'identifier' => $this->identifier,
            'title' => $this->title,
            'content' => $this->content,
            'is_active' => $this->is_active,
            'store_view_ids' => $this->whenLoaded('storeViews', fn () => $this->storeViews->pluck('id')->toArray()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
