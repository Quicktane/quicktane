<?php

declare(strict_types=1);

namespace App\Directory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
            'symbol' => ['sometimes', 'string', 'max:10'],
            'decimal_places' => ['sometimes', 'integer', 'min:0', 'max:6'],
        ];
    }
}
