<?php

declare(strict_types=1);

namespace App\Payment\Facades;

use App\Payment\Contracts\PaymentFacade as PaymentFacadeContract;
use App\Payment\DataTransferObjects\PaymentRequest;
use App\Payment\DataTransferObjects\PaymentResponse;
use App\Payment\Enums\PaymentStatus;
use App\Payment\Models\Transaction;
use App\Payment\Services\PaymentService;
use Illuminate\Support\Collection;

class PaymentFacade implements PaymentFacadeContract
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    public function authorize(string $paymentMethodCode, PaymentRequest $paymentRequest): PaymentResponse
    {
        return $this->paymentService->authorize($paymentMethodCode, $paymentRequest);
    }

    public function capture(int $transactionId, ?string $amount = null): PaymentResponse
    {
        return $this->paymentService->capture($transactionId, $amount);
    }

    public function void(int $transactionId): PaymentResponse
    {
        return $this->paymentService->void($transactionId);
    }

    public function refund(int $transactionId, ?string $amount = null): PaymentResponse
    {
        return $this->paymentService->refund($transactionId, $amount);
    }

    public function getTransaction(int $id): ?Transaction
    {
        return $this->paymentService->getTransaction($id);
    }

    public function getTransactionByReference(string $reference): ?Transaction
    {
        return $this->paymentService->getTransactionByReference($reference);
    }

    public function getTransactionsByOrder(int $orderId): Collection
    {
        return $this->paymentService->getTransactionsByOrder($orderId);
    }

    public function getActivePaymentMethods(): Collection
    {
        return $this->paymentService->getActivePaymentMethods();
    }

    public function getPaymentStatus(int $orderId): PaymentStatus
    {
        return $this->paymentService->getPaymentStatus($orderId);
    }
}
