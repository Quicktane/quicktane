<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'countries' => ['sometimes', 'array'],
            'countries.*.country_id' => ['required_with:countries', 'integer', 'exists:countries,id'],
            'countries.*.region_id' => ['sometimes', 'nullable', 'integer', 'exists:regions,id'],
        ];
    }
}
