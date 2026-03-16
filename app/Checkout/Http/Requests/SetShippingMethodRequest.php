<?php

declare(strict_types=1);

namespace App\Checkout\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetShippingMethodRequest extends FormRequest
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
            'session_uuid' => ['required', 'string'],
            'carrier_code' => ['required', 'string'],
            'method_code' => ['required', 'string'],
        ];
    }
}
