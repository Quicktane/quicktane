<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UrlRewriteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'entity_type' => $this->entity_type?->value,
            'entity_id' => $this->entity_id,
            'request_path' => $this->request_path,
            'target_path' => $this->target_path,
            'redirect_type' => $this->redirect_type?->value,
            'store_view_id' => $this->store_view_id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
