<?php

declare(strict_types=1);

namespace Quicktane\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240', 'mimes:jpeg,png,gif,webp,svg,pdf'],
        ];
    }
}
