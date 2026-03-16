<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['sometimes', 'string', 'max:255', 'unique:shipping_methods,code,'.$this->route('method')->id],
            'name' => ['sometimes', 'string', 'max:255'],
            'carrier_code' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
            'min_order_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_order_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'config' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
