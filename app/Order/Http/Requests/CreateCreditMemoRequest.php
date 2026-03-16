<?php

declare(strict_types=1);

namespace App\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCreditMemoRequest extends FormRequest
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
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'adjustment_positive' => ['nullable', 'numeric', 'min:0'],
            'adjustment_negative' => ['nullable', 'numeric', 'min:0'],
            'refund_shipping' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
