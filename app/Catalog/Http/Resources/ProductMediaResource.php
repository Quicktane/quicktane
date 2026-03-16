<?php

declare(strict_types=1);

namespace App\Catalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'media_file_id' => $this->id,
            'uuid' => $this->uuid,
            'filename' => $this->filename,
            'mime_type' => $this->mime_type,
            'url' => $this->url,
            'alt_text' => $this->alt_text,
            'position' => $this->pivot->position,
            'label' => $this->pivot->label,
            'is_main' => (bool) $this->pivot->is_main,
        ];
    }
}
