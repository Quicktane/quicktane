<?php

declare(strict_types=1);

namespace App\Store\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => ['required', 'exists:stores,id'],
            'code' => ['required', 'string', 'max:255', 'unique:store_views,code'],
            'name' => ['required', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'max:10'],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
