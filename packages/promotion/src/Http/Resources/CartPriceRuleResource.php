<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartPriceRuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'from_date' => $this->from_date?->toDateString(),
            'to_date' => $this->to_date?->toDateString(),
            'priority' => $this->priority,
            'stop_further_processing' => $this->stop_further_processing,
            'action_type' => $this->action_type->value,
            'action_amount' => $this->action_amount,
            'max_discount_amount' => $this->max_discount_amount,
            'apply_to_shipping' => $this->apply_to_shipping,
            'times_used' => $this->times_used,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'conditions' => RuleConditionResource::collection($this->whenLoaded('conditions')),
            'coupons' => CouponResource::collection($this->whenLoaded('coupons')),
        ];
    }
}
