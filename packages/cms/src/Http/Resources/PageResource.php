<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'layout' => $this->layout?->value,
            'store_view_ids' => $this->whenLoaded('storeViews', fn () => $this->storeViews->pluck('id')->toArray()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
