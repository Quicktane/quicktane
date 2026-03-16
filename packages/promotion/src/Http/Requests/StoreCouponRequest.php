<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255', 'unique:coupons,code'],
            'cart_price_rule_id' => ['required', 'exists:cart_price_rules,id'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_per_customer' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'expires_at' => ['nullable', 'date'],
        ];
    }
}
