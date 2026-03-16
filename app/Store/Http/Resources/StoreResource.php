<?php

declare(strict_types=1);

namespace App\Store\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'code' => $this->code,
            'name' => $this->name,
            'website_id' => $this->website_id,
            'root_category_id' => $this->root_category_id,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'website' => new WebsiteResource($this->whenLoaded('website')),
            'store_views' => StoreViewResource::collection($this->whenLoaded('storeViews')),
        ];
    }
}
