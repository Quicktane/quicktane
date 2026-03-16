<?php

declare(strict_types=1);

namespace Quicktane\Notification\Mail;

use App\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangeMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly string $fromStatus,
        public readonly string $toStatus,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order #{$this->order->increment_id} Status Update",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'notification::emails.order.status-change',
            with: [
                'order' => $this->order,
                'fromStatus' => $this->fromStatus,
                'toStatus' => $this->toStatus,
            ],
        );
    }
}
