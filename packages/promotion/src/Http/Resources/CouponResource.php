<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'code' => $this->code,
            'usage_limit' => $this->usage_limit,
            'usage_per_customer' => $this->usage_per_customer,
            'times_used' => $this->times_used,
            'is_active' => $this->is_active,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'rule' => new CartPriceRuleResource($this->whenLoaded('rule')),
        ];
    }
}
