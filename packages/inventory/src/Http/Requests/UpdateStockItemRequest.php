<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'source_id' => ['required', 'integer', 'exists:inventory_sources,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'notify_quantity' => ['sometimes', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
