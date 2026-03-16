<?php

declare(strict_types=1);

namespace App\Store\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'path' => ['required', 'string'],
            'value' => ['nullable', 'string'],
            'scope' => ['sometimes', 'string', 'in:global,website,store,store_view'],
            'scope_id' => ['sometimes', 'integer'],
        ];
    }
}
