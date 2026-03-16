<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUrlRewriteRequest extends FormRequest
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
            'entity_type' => ['required', 'string', 'in:product,category,cms_page,custom'],
            'entity_id' => ['nullable', 'integer'],
            'request_path' => ['required', 'string', 'max:255'],
            'target_path' => ['required', 'string', 'max:255'],
            'redirect_type' => ['nullable', 'integer', 'in:301,302'],
            'store_view_id' => ['sometimes', 'integer'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
