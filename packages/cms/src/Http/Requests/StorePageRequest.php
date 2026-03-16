<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
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
            'identifier' => ['required', 'string', 'max:255', 'unique:cms_pages,identifier'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
            'layout' => ['sometimes', 'string', 'in:one_column,two_columns_left,two_columns_right,empty'],
            'store_view_ids' => ['sometimes', 'array'],
            'store_view_ids.*' => ['integer'],
        ];
    }
}
