<?php

declare(strict_types=1);

namespace App\Directory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'base_currency_code' => ['required', 'string', 'size:3', 'exists:currencies,code'],
            'target_currency_code' => ['required', 'string', 'size:3', 'exists:currencies,code', 'different:base_currency_code'],
            'rate' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
