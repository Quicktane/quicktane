<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxRateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'tax_zone_id' => ['required', 'exists:tax_zones,id'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'priority' => ['sometimes', 'integer'],
            'is_compound' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
