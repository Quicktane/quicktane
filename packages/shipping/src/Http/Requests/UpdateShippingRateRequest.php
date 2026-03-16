<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_method_id' => ['sometimes', 'integer', 'exists:shipping_methods,id'],
            'shipping_zone_id' => ['sometimes', 'integer', 'exists:shipping_zones,id'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'min_weight' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_weight' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'min_subtotal' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_subtotal' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
