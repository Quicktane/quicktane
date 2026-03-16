<?php

declare(strict_types=1);

namespace App\Directory\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'base_currency_code' => $this->base_currency_code,
            'target_currency_code' => $this->target_currency_code,
            'rate' => $this->rate,
            'updated_at' => $this->updated_at,
        ];
    }
}
