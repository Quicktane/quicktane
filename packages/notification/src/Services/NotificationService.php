<?php

declare(strict_types=1);

namespace Quicktane\Notification\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Quicktane\Notification\Enums\NotificationChannel;
use Quicktane\Notification\Enums\NotificationStatus;
use Quicktane\Notification\Enums\TemplateCode;
use Quicktane\Notification\Mail\CustomerWelcomeMail;
use Quicktane\Notification\Mail\OrderConfirmationMail;
use Quicktane\Notification\Mail\OrderStatusChangeMail;
use Quicktane\Notification\Models\NotificationLog;
use Quicktane\Notification\Repositories\NotificationLogRepository;

class NotificationService
{
    public function __construct(
        private readonly NotificationLogRepository $notificationLogRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function send(TemplateCode $templateCode, string $recipient, array $data, int $storeViewId = 0): NotificationLog
    {
        $log = $this->logNotification(
            channel: NotificationChannel::Email,
            templateCode: $templateCode,
            recipient: $recipient,
            data: $data,
            storeViewId: $storeViewId,
        );

        try {
            $mailable = $this->resolveMailable($templateCode, $data);

            if ($mailable === null) {
                return $this->markFailed($log, "No mailable configured for template: {$templateCode->value}");
            }

            Mail::to($recipient)->send($mailable);

            return $this->markSent($log, $mailable->subject ?? '');
        } catch (\Throwable $e) {
            return $this->markFailed($log, $e->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function logNotification(
        NotificationChannel $channel,
        TemplateCode $templateCode,
        string $recipient,
        array $data,
        int $storeViewId = 0,
    ): NotificationLog {
        return $this->notificationLogRepository->create([
            'channel' => $channel->value,
            'template_code' => $templateCode->value,
            'recipient' => $recipient,
            'status' => NotificationStatus::Pending->value,
            'variables' => $data,
            'store_view_id' => $storeViewId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveMailable(TemplateCode $templateCode, array $data): ?Mailable
    {
        return match ($templateCode) {
            TemplateCode::OrderConfirmation => new OrderConfirmationMail($data['order']),
            TemplateCode::OrderStatusChange => new OrderStatusChangeMail($data['order'], $data['from_status'], $data['to_status']),
            TemplateCode::CustomerWelcome => new CustomerWelcomeMail($data['customer']),
            default => null,
        };
    }

    private function markSent(NotificationLog $log, string $subject): NotificationLog
    {
        return $this->notificationLogRepository->update($log, [
            'status' => NotificationStatus::Sent->value,
            'subject' => $subject,
            'sent_at' => now(),
        ]);
    }

    private function markFailed(NotificationLog $log, string $errorMessage): NotificationLog
    {
        return $this->notificationLogRepository->update($log, [
            'status' => NotificationStatus::Failed->value,
            'error_message' => $errorMessage,
        ]);
    }
}
