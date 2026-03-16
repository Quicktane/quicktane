<?php

declare(strict_types=1);

namespace Quicktane\Notification\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'channel' => $this->channel?->value,
            'template_code' => $this->template_code,
            'recipient' => $this->recipient,
            'subject' => $this->subject,
            'status' => $this->status?->value,
            'error_message' => $this->error_message,
            'store_view_id' => $this->store_view_id,
            'sent_at' => $this->sent_at,
            'created_at' => $this->created_at,
        ];
    }
}
