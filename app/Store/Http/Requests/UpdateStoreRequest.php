<?php

declare(strict_types=1);

namespace App\Store\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'website_id' => ['sometimes', 'exists:websites,id'],
            'code' => ['sometimes', 'string', 'max:255', 'unique:stores,code,'.$this->route('store')->id],
            'name' => ['sometimes', 'string', 'max:255'],
            'root_category_id' => ['nullable', 'integer'],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
