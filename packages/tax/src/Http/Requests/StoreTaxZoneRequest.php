<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
            'rules' => ['nullable', 'array'],
            'rules.*.country_id' => ['required_with:rules', 'exists:countries,id'],
            'rules.*.region_id' => ['nullable', 'exists:regions,id'],
            'rules.*.postcode_from' => ['nullable', 'string'],
            'rules.*.postcode_to' => ['nullable', 'string'],
        ];
    }
}
