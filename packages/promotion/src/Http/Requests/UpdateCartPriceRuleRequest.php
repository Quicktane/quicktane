<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartPriceRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'priority' => ['sometimes', 'integer', 'min:0'],
            'stop_further_processing' => ['sometimes', 'boolean'],
            'action_type' => ['sometimes', 'string', 'in:by_percent,by_fixed,buy_x_get_y,free_shipping'],
            'action_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'apply_to_shipping' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
            'conditions' => ['nullable', 'array'],
            'conditions.*.type' => ['required_with:conditions', 'string', 'in:combine,cart_attribute,product_attribute,customer_attribute'],
            'conditions.*.attribute' => ['nullable', 'string'],
            'conditions.*.operator' => ['nullable', 'string'],
            'conditions.*.value' => ['nullable', 'string'],
            'conditions.*.aggregator' => ['nullable', 'string', 'in:all,any'],
            'conditions.*.is_inverted' => ['boolean'],
            'conditions.*.sort_order' => ['integer'],
            'conditions.*.children' => ['nullable', 'array'],
            'coupons' => ['nullable', 'array'],
            'coupons.*.code' => ['required_with:coupons', 'string'],
            'coupons.*.usage_limit' => ['nullable', 'integer', 'min:1'],
            'coupons.*.usage_per_customer' => ['nullable', 'integer', 'min:1'],
            'coupons.*.is_active' => ['boolean'],
            'coupons.*.expires_at' => ['nullable', 'date'],
        ];
    }
}
