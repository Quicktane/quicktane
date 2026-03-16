<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventorySourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['sometimes', 'string', 'max:255', 'unique:inventory_sources,code,'.$this->route('source')->id],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'country_code' => ['sometimes', 'nullable', 'string', 'max:2'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
        ];
    }
}
