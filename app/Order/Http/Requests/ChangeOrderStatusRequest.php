<?php

declare(strict_types=1);

namespace App\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeOrderStatusRequest extends FormRequest
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
            'status' => ['required', 'string', 'in:pending,processing,on_hold,shipped,delivered,completed,canceled,returned,refunded'],
            'comment' => ['nullable', 'string'],
            'notify_customer' => ['sometimes', 'boolean'],
        ];
    }
}
