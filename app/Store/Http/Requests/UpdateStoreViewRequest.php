<?php

declare(strict_types=1);

namespace App\Store\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => ['sometimes', 'exists:stores,id'],
            'code' => ['sometimes', 'string', 'max:255', 'unique:store_views,code,'.$this->route('store_view')->id],
            'name' => ['sometimes', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'max:10'],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
