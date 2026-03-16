<?php

declare(strict_types=1);

namespace App\Payment\Contracts;

use App\Payment\DataTransferObjects\PaymentRequest;
use App\Payment\DataTransferObjects\PaymentResponse;
use App\Payment\Enums\PaymentStatus;
use App\Payment\Models\PaymentMethod;
use App\Payment\Models\Transaction;
use Illuminate\Support\Collection;

interface PaymentFacade
{
    public function authorize(string $paymentMethodCode, PaymentRequest $paymentRequest): PaymentResponse;

    public function capture(int $transactionId, ?string $amount = null): PaymentResponse;

    public function void(int $transactionId): PaymentResponse;

    public function refund(int $transactionId, ?string $amount = null): PaymentResponse;

    public function getTransaction(int $id): ?Transaction;

    public function getTransactionByReference(string $reference): ?Transaction;

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactionsByOrder(int $orderId): Collection;

    /**
     * @return Collection<int, PaymentMethod>
     */
    public function getActivePaymentMethods(): Collection;

    public function getPaymentStatus(int $orderId): PaymentStatus;
}
