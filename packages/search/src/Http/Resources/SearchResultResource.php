<?php

declare(strict_types=1);

namespace Quicktane\Search\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'products' => $this->resource['products'] ?? [],
            'total' => $this->resource['total'] ?? 0,
            'facets' => $this->resource['facets'] ?? [],
        ];
    }
}
