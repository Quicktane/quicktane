<?php

declare(strict_types=1);

namespace App\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeSetRequest extends FormRequest
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
        $attributeSetId = $this->route('attribute_set')?->id;

        return [
            'name' => ['sometimes', 'string', 'max:255', 'unique:attribute_sets,name,'.$attributeSetId],
            'sort_order' => ['sometimes', 'integer'],
        ];
    }
}
