<?php

declare(strict_types=1);

namespace App\Payment\Services;

use App\Payment\Contracts\PaymentGateway;
use App\Payment\DataTransferObjects\PaymentRequest;
use App\Payment\DataTransferObjects\PaymentResponse;

class OfflineGateway implements PaymentGateway
{
    public function code(): string
    {
        return 'offline';
    }

    public function name(): string
    {
        return 'Offline Payment';
    }

    public function authorize(PaymentRequest $paymentRequest): PaymentResponse
    {
        return new PaymentResponse(
            success: true,
            transactionReference: 'offline_'.uniqid(),
        );
    }

    public function capture(string $transactionReference, string $amount): PaymentResponse
    {
        return new PaymentResponse(
            success: true,
            transactionReference: $transactionReference,
        );
    }

    public function void(string $transactionReference): PaymentResponse
    {
        return new PaymentResponse(
            success: true,
            transactionReference: $transactionReference,
        );
    }

    public function refund(string $transactionReference, string $amount): PaymentResponse
    {
        return new PaymentResponse(
            success: true,
            transactionReference: $transactionReference,
        );
    }

    public function supportsCapture(): bool
    {
        return false;
    }

    public function requiresRedirect(): bool
    {
        return false;
    }
}
