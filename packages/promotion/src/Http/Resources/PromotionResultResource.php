<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_discount' => $this->totalDiscount,
            'free_shipping' => $this->freeShipping,
            'discounts' => array_map(fn ($discountResult): array => [
                'rule_id' => $discountResult->ruleId,
                'rule_name' => $discountResult->ruleName,
                'discount_amount' => $discountResult->discountAmount,
                'action_type' => $discountResult->actionType->value,
                'coupon_code' => $discountResult->couponCode,
            ], $this->discounts),
        ];
    }
}
