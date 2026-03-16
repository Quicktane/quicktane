<?php

declare(strict_types=1);

namespace App\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginCustomerRequest extends FormRequest
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
            'store_id' => ['required', 'exists:stores,id'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
