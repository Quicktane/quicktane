<?php

declare(strict_types=1);

namespace App\Store\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'website_id' => ['required', 'exists:websites,id'],
            'code' => ['required', 'string', 'max:255', 'unique:stores,code'],
            'name' => ['required', 'string', 'max:255'],
            'root_category_id' => ['nullable', 'integer'],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
