<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxClassRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:product,customer'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
