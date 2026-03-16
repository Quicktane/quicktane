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

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order Confirmation #{$this->order->increment_id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'notification::emails.order.confirmation',
            with: [
                'order' => $this->order,
            ],
        );
    }
}
