<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstimateShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => ['required', 'string', 'max:2', 'exists:countries,iso2'],
            'region_id' => ['sometimes', 'nullable', 'integer', 'exists:regions,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'total_weight' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'currency_code' => ['required', 'string', 'max:3'],
        ];
    }
}
