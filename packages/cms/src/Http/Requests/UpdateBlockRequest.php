<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlockRequest extends FormRequest
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
        $blockId = $this->route('block')?->id;

        return [
            'identifier' => ['sometimes', 'string', 'max:255', "unique:cms_blocks,identifier,{$blockId}"],
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'store_view_ids' => ['sometimes', 'array'],
            'store_view_ids.*' => ['integer'],
        ];
    }
}
