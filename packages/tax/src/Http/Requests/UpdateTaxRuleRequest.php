<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxRuleRequest extends FormRequest
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
            'tax_rate_id' => ['sometimes', 'exists:tax_rates,id'],
            'product_tax_class_id' => ['sometimes', 'exists:tax_classes,id'],
            'customer_tax_class_id' => ['sometimes', 'exists:tax_classes,id'],
            'priority' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
