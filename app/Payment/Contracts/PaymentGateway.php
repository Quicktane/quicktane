<?php

declare(strict_types=1);

namespace App\Payment\Contracts;

use App\Payment\DataTransferObjects\PaymentRequest;
use App\Payment\DataTransferObjects\PaymentResponse;

interface PaymentGateway
{
    public function code(): string;

    public function name(): string;

    public function authorize(PaymentRequest $paymentRequest): PaymentResponse;

    public function capture(string $transactionReference, string $amount): PaymentResponse;

    public function void(string $transactionReference): PaymentResponse;

    public function refund(string $transactionReference, string $amount): PaymentResponse;

    public function supportsCapture(): bool;

    public function requiresRedirect(): bool;
}
