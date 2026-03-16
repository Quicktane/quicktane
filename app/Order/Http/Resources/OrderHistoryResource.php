<?php

declare(strict_types=1);

namespace App\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderHistoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'comment' => $this->comment,
            'is_customer_notified' => $this->is_customer_notified,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
        ];
    }
}
