<?php

declare(strict_types=1);

namespace Quicktane\Search\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSearchSynonymRequest extends FormRequest
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
            'term' => ['sometimes', 'string', 'max:255'],
            'synonyms' => ['sometimes', 'array'],
            'synonyms.*' => ['string', 'max:255'],
            'store_view_id' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
