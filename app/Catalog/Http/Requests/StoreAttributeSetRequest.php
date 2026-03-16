<?php

declare(strict_types=1);

namespace App\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeSetRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:attribute_sets,name'],
            'sort_order' => ['sometimes', 'integer'],
        ];
    }
}
