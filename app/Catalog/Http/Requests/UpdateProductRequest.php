<?php

declare(strict_types=1);

namespace App\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id;

        return [
            'type' => ['sometimes', 'string', 'in:simple,configurable,bundle,virtual,downloadable'],
            'attribute_set_uuid' => ['sometimes', 'string', 'exists:attribute_sets,uuid'],
            'sku' => ['sometimes', 'string', 'max:255', 'unique:products,sku,'.$productId],
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:products,slug,'.$productId],
            'description' => ['sometimes', 'nullable', 'string'],
            'short_description' => ['sometimes', 'nullable', 'string'],
            'base_price' => ['sometimes', 'numeric', 'min:0'],
            'special_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'special_price_from' => ['sometimes', 'nullable', 'date'],
            'special_price_to' => ['sometimes', 'nullable', 'date'],
            'cost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'weight' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'meta_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'meta_description' => ['sometimes', 'nullable', 'string'],
            'attribute_values' => ['sometimes', 'array'],
            'attribute_values.*.attribute_uuid' => ['required_with:attribute_values', 'string', 'exists:attributes,uuid'],
            'attribute_values.*.value' => ['sometimes', 'nullable', 'string'],
            'category_uuids' => ['sometimes', 'array'],
            'category_uuids.*' => ['string', 'exists:categories,uuid'],
            'media' => ['sometimes', 'array'],
            'media.*.media_file_uuid' => ['required_with:media', 'string', 'exists:media_files,uuid'],
            'media.*.position' => ['sometimes', 'integer'],
            'media.*.label' => ['sometimes', 'nullable', 'string'],
            'media.*.is_main' => ['sometimes', 'boolean'],
        ];
    }
}
