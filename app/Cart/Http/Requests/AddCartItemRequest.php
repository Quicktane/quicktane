<?php

declare(strict_types=1);

namespace App\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
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
            'product_uuid' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'options' => ['nullable', 'array'],
            'store_id' => ['required', 'exists:stores,id'],
            'currency_code' => ['required', 'string', 'size:3'],
        ];
    }
}
