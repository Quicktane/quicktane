<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustStockRequest extends FormRequest
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
            'quantity_change' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
