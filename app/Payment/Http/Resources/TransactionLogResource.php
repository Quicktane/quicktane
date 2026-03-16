<?php

declare(strict_types=1);

namespace App\Payment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'status' => $this->status,
            'request_data' => $this->request_data,
            'response_data' => $this->response_data,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at,
        ];
    }
}
