<?php

declare(strict_types=1);

namespace App\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => $this->status->value,
            'currency_code' => $this->currency_code,
            'items_count' => $this->items_count,
            'subtotal' => $this->subtotal,
            'guest_token' => $this->guest_token,
            'customer' => $this->when($this->customer_id !== null, function () {
                $customer = $this->customer;

                return $customer !== null ? [
                    'uuid' => $customer->uuid,
                    'email' => $customer->email,
                    'name' => $customer->first_name.' '.$customer->last_name,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
