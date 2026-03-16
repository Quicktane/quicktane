<?php

declare(strict_types=1);

namespace App\Payment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'order_id' => $this->order_id,
            'payment_method_code' => $this->payment_method_code,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'amount' => $this->amount,
            'currency_code' => $this->currency_code,
            'reference_id' => $this->reference_id,
            'parent_transaction_id' => $this->parent_transaction_id,
            'metadata' => $this->metadata,
            'logs' => TransactionLogResource::collection($this->whenLoaded('logs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
