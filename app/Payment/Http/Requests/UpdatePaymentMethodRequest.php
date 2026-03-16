<?php

declare(strict_types=1);

namespace App\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest
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
        $paymentMethod = $this->route('paymentMethod');
        $paymentMethodId = $paymentMethod?->id;

        return [
            'code' => ['sometimes', 'string', 'max:255', 'unique:payment_methods,code,'.$paymentMethodId],
            'name' => ['sometimes', 'string', 'max:255'],
            'gateway_code' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_order_amount' => ['nullable', 'numeric', 'min:0'],
            'config' => ['nullable', 'array'],
        ];
    }
}
