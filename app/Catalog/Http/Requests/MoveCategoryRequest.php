<?php

declare(strict_types=1);

namespace App\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveCategoryRequest extends FormRequest
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
            'parent_id' => ['required', 'nullable', 'integer', 'exists:categories,id'],
            'position' => ['sometimes', 'integer'],
        ];
    }
}
