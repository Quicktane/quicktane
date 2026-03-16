<?php

declare(strict_types=1);

namespace App\Store\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'path' => ['required', 'string'],
            'scope' => ['sometimes', 'string', 'in:global,website,store,store_view'],
            'scope_id' => ['sometimes', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'scope' => $this->query('scope', 'global'),
            'scope_id' => (int) $this->query('scope_id', '0'),
        ]);
    }
}
