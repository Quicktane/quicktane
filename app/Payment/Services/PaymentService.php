<?php

declare(strict_types=1);

namespace App\Payment\Services;

use App\Payment\DataTransferObjects\PaymentRequest;
use App\Payment\DataTransferObjects\PaymentResponse;
use App\Payment\Enums\PaymentStatus;
use App\Payment\Enums\TransactionStatus;
use App\Payment\Enums\TransactionType;
use App\Payment\Events\AfterPaymentAuthorize;
use App\Payment\Events\AfterPaymentCapture;
use App\Payment\Events\AfterPaymentRefund;
use App\Payment\Events\AfterPaymentVoid;
use App\Payment\Events\BeforePaymentAuthorize;
use App\Payment\Events\BeforePaymentCapture;
use App\Payment\Models\PaymentMethod;
use App\Payment\Models\Transaction;
use App\Payment\Repositories\PaymentMethodRepository;
use App\Payment\Repositories\TransactionLogRepository;
use App\Payment\Repositories\TransactionRepository;
use Illuminate\Support\Collection;
use Quicktane\Core\Events\EventDispatcher;
use Quicktane\Core\Events\OperationContext;
use RuntimeException;

class PaymentService
{
    public function __construct(
        private readonly PaymentGatewayRegistry $paymentGatewayRegistry,
        private readonly PaymentMethodRepository $paymentMethodRepository,
        private readonly TransactionRepository $transactionRepository,
        private readonly TransactionLogRepository $transactionLogRepository,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    public function authorize(string $paymentMethodCode, PaymentRequest $paymentRequest): PaymentResponse
    {
        $paymentMethod = $this->paymentMethodRepository->findByCode($paymentMethodCode);

        if ($paymentMethod === null) {
            throw new RuntimeException("Payment method not found: {$paymentMethodCode}");
        }

        $gateway = $this->paymentGatewayRegistry->getGateway($paymentMethod->gateway_code);

        if ($gateway === null) {
            throw new RuntimeException("Payment gateway not found: {$paymentMethod->gateway_code}");
        }

        $operationContext = new OperationContext;
        $operationContext->set('payment_method_code', $paymentMethodCode);
        $operationContext->set('amount', $paymentRequest->amount);
        $operationContext->set('currency_code', $paymentRequest->currencyCode);

        $this->eventDispatcher->dispatch(new BeforePaymentAuthorize($operationContext));

        $paymentResponse = $gateway->authorize($paymentRequest);

        $transactionStatus = $paymentResponse->success
            ? TransactionStatus::Success
            : TransactionStatus::Failed;

        $transaction = $this->transactionRepository->create([
            'order_id' => $paymentRequest->orderId,
            'payment_method_code' => $paymentMethodCode,
            'type' => TransactionType::Authorize,
            'status' => $transactionStatus,
            'amount' => $paymentRequest->amount,
            'currency_code' => $paymentRequest->currencyCode,
            'reference_id' => $paymentResponse->transactionReference,
            'metadata' => $paymentResponse->metadata,
        ]);

        $this->transactionLogRepository->create([
            'transaction_id' => $transaction->id,
            'action' => 'authorize',
            'status' => $transactionStatus->value,
            'request_data' => [
                'amount' => $paymentRequest->amount,
                'currency_code' => $paymentRequest->currencyCode,
                'order_id' => $paymentRequest->orderId,
            ],
            'response_data' => [
                'success' => $paymentResponse->success,
                'transaction_reference' => $paymentResponse->transactionReference,
                'redirect_url' => $paymentResponse->redirectUrl,
            ],
            'error_message' => $paymentResponse->errorMessage,
        ]);

        if ($paymentResponse->success) {
            $this->eventDispatcher->dispatch(new AfterPaymentAuthorize($transaction, $operationContext));
        }

        return $paymentResponse;
    }

    public function capture(int $transactionId, ?string $amount = null): PaymentResponse
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if ($transaction === null) {
            throw new RuntimeException("Transaction not found: {$transactionId}");
        }

        $paymentMethod = $this->paymentMethodRepository->findByCode($transaction->payment_method_code);

        if ($paymentMethod === null) {
            throw new RuntimeException("Payment method not found: {$transaction->payment_method_code}");
        }

        $gateway = $this->paymentGatewayRegistry->getGateway($paymentMethod->gateway_code);

        if ($gateway === null) {
            throw new RuntimeException("Payment gateway not found: {$paymentMethod->gateway_code}");
        }

        $captureAmount = $amount ?? $transaction->amount;

        $operationContext = new OperationContext;
        $operationContext->set('transaction_id', $transactionId);
        $operationContext->set('amount', $captureAmount);

        $this->eventDispatcher->dispatch(new BeforePaymentCapture($transaction, $operationContext));

        $paymentResponse = $gateway->capture($transaction->reference_id, $captureAmount);

        $transactionStatus = $paymentResponse->success
            ? TransactionStatus::Success
            : TransactionStatus::Failed;

        $childTransaction = $this->transactionRepository->create([
            'order_id' => $transaction->order_id,
            'payment_method_code' => $transaction->payment_method_code,
            'type' => TransactionType::Capture,
            'status' => $transactionStatus,
            'amount' => $captureAmount,
            'currency_code' => $transaction->currency_code,
            'reference_id' => $paymentResponse->transactionReference,
            'parent_transaction_id' => $transaction->id,
            'metadata' => $paymentResponse->metadata,
        ]);

        $this->transactionLogRepository->create([
            'transaction_id' => $childTransaction->id,
            'action' => 'capture',
            'status' => $transactionStatus->value,
            'request_data' => [
                'parent_transaction_id' => $transaction->id,
                'amount' => $captureAmount,
                'reference_id' => $transaction->reference_id,
            ],
            'response_data' => [
                'success' => $paymentResponse->success,
                'transaction_reference' => $paymentResponse->transactionReference,
            ],
            'error_message' => $paymentResponse->errorMessage,
        ]);

        if ($paymentResponse->success) {
            $this->eventDispatcher->dispatch(new AfterPaymentCapture($childTransaction, $operationContext));
        }

        return $paymentResponse;
    }

    public function void(int $transactionId): PaymentResponse
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if ($transaction === null) {
            throw new RuntimeException("Transaction not found: {$transactionId}");
        }

        $paymentMethod = $this->paymentMethodRepository->findByCode($transaction->payment_method_code);

        if ($paymentMethod === null) {
            throw new RuntimeException("Payment method not found: {$transaction->payment_method_code}");
        }

        $gateway = $this->paymentGatewayRegistry->getGateway($paymentMethod->gateway_code);

        if ($gateway === null) {
            throw new RuntimeException("Payment gateway not found: {$paymentMethod->gateway_code}");
        }

        $operationContext = new OperationContext;
        $operationContext->set('transaction_id', $transactionId);

        $paymentResponse = $gateway->void($transaction->reference_id);

        $transactionStatus = $paymentResponse->success
            ? TransactionStatus::Success
            : TransactionStatus::Failed;

        $childTransaction = $this->transactionRepository->create([
            'order_id' => $transaction->order_id,
            'payment_method_code' => $transaction->payment_method_code,
            'type' => TransactionType::Void,
            'status' => $transactionStatus,
            'amount' => $transaction->amount,
            'currency_code' => $transaction->currency_code,
            'reference_id' => $paymentResponse->transactionReference,
            'parent_transaction_id' => $transaction->id,
            'metadata' => $paymentResponse->metadata,
        ]);

        $this->transactionLogRepository->create([
            'transaction_id' => $childTransaction->id,
            'action' => 'void',
            'status' => $transactionStatus->value,
            'request_data' => [
                'parent_transaction_id' => $transaction->id,
                'reference_id' => $transaction->reference_id,
            ],
            'response_data' => [
                'success' => $paymentResponse->success,
                'transaction_reference' => $paymentResponse->transactionReference,
            ],
            'error_message' => $paymentResponse->errorMessage,
        ]);

        if ($paymentResponse->success) {
            $this->eventDispatcher->dispatch(new AfterPaymentVoid($childTransaction, $operationContext));
        }

        return $paymentResponse;
    }

    public function refund(int $transactionId, ?string $amount = null): PaymentResponse
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if ($transaction === null) {
            throw new RuntimeException("Transaction not found: {$transactionId}");
        }

        $paymentMethod = $this->paymentMethodRepository->findByCode($transaction->payment_method_code);

        if ($paymentMethod === null) {
            throw new RuntimeException("Payment method not found: {$transaction->payment_method_code}");
        }

        $gateway = $this->paymentGatewayRegistry->getGateway($paymentMethod->gateway_code);

        if ($gateway === null) {
            throw new RuntimeException("Payment gateway not found: {$paymentMethod->gateway_code}");
        }

        $refundAmount = $amount ?? $transaction->amount;

        $operationContext = new OperationContext;
        $operationContext->set('transaction_id', $transactionId);
        $operationContext->set('amount', $refundAmount);

        $paymentResponse = $gateway->refund($transaction->reference_id, $refundAmount);

        $transactionStatus = $paymentResponse->success
            ? TransactionStatus::Success
            : TransactionStatus::Failed;

        $childTransaction = $this->transactionRepository->create([
            'order_id' => $transaction->order_id,
            'payment_method_code' => $transaction->payment_method_code,
            'type' => TransactionType::Refund,
            'status' => $transactionStatus,
            'amount' => $refundAmount,
            'currency_code' => $transaction->currency_code,
            'reference_id' => $paymentResponse->transactionReference,
            'parent_transaction_id' => $transaction->id,
            'metadata' => $paymentResponse->metadata,
        ]);

        $this->transactionLogRepository->create([
            'transaction_id' => $childTransaction->id,
            'action' => 'refund',
            'status' => $transactionStatus->value,
            'request_data' => [
                'parent_transaction_id' => $transaction->id,
                'amount' => $refundAmount,
                'reference_id' => $transaction->reference_id,
            ],
            'response_data' => [
                'success' => $paymentResponse->success,
                'transaction_reference' => $paymentResponse->transactionReference,
            ],
            'error_message' => $paymentResponse->errorMessage,
        ]);

        if ($paymentResponse->success) {
            $this->eventDispatcher->dispatch(new AfterPaymentRefund($childTransaction, $operationContext));
        }

        return $paymentResponse;
    }

    public function getTransaction(int $id): ?Transaction
    {
        return $this->transactionRepository->findById($id);
    }

    public function getTransactionByReference(string $reference): ?Transaction
    {
        return $this->transactionRepository->findByReference($reference);
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactionsByOrder(int $orderId): Collection
    {
        return $this->transactionRepository->findByOrder($orderId);
    }

    /**
     * @return Collection<int, PaymentMethod>
     */
    public function getActivePaymentMethods(): Collection
    {
        return $this->paymentMethodRepository->findActive();
    }

    public function getPaymentStatus(int $orderId): PaymentStatus
    {
        $transactions = $this->transactionRepository->findByOrder($orderId);

        if ($transactions->isEmpty()) {
            return PaymentStatus::Pending;
        }

        $successfulTransactions = $transactions->filter(
            fn (Transaction $transaction): bool => $transaction->status === TransactionStatus::Success,
        );

        $hasSuccessfulVoid = $successfulTransactions->contains(
            fn (Transaction $transaction): bool => $transaction->type === TransactionType::Void,
        );

        if ($hasSuccessfulVoid) {
            return PaymentStatus::Voided;
        }

        $hasSuccessfulCapture = $successfulTransactions->contains(
            fn (Transaction $transaction): bool => $transaction->type === TransactionType::Capture,
        );

        $hasSuccessfulRefund = $successfulTransactions->contains(
            fn (Transaction $transaction): bool => $transaction->type === TransactionType::Refund,
        );

        if ($hasSuccessfulCapture && $hasSuccessfulRefund) {
            $capturedAmount = $successfulTransactions
                ->filter(fn (Transaction $transaction): bool => $transaction->type === TransactionType::Capture)
                ->reduce(fn (string $carry, Transaction $transaction): string => bcadd($carry, $transaction->amount, 4), '0.0000');

            $refundedAmount = $successfulTransactions
                ->filter(fn (Transaction $transaction): bool => $transaction->type === TransactionType::Refund)
                ->reduce(fn (string $carry, Transaction $transaction): string => bcadd($carry, $transaction->amount, 4), '0.0000');

            if (bccomp($refundedAmount, $capturedAmount, 4) >= 0) {
                return PaymentStatus::Refunded;
            }

            return PaymentStatus::PartiallyRefunded;
        }

        if ($hasSuccessfulRefund) {
            $authorizedAmount = $successfulTransactions
                ->filter(fn (Transaction $transaction): bool => $transaction->type === TransactionType::Authorize)
                ->reduce(fn (string $carry, Transaction $transaction): string => bcadd($carry, $transaction->amount, 4), '0.0000');

            $refundedAmount = $successfulTransactions
                ->filter(fn (Transaction $transaction): bool => $transaction->type === TransactionType::Refund)
                ->reduce(fn (string $carry, Transaction $transaction): string => bcadd($carry, $transaction->amount, 4), '0.0000');

            if (bccomp($refundedAmount, $authorizedAmount, 4) >= 0) {
                return PaymentStatus::Refunded;
            }

            return PaymentStatus::PartiallyRefunded;
        }

        if ($hasSuccessfulCapture) {
            return PaymentStatus::Captured;
        }

        $hasSuccessfulAuthorize = $successfulTransactions->contains(
            fn (Transaction $transaction): bool => $transaction->type === TransactionType::Authorize,
        );

        if ($hasSuccessfulAuthorize) {
            return PaymentStatus::Authorized;
        }

        $allFailed = $transactions->every(
            fn (Transaction $transaction): bool => $transaction->status === TransactionStatus::Failed,
        );

        if ($allFailed) {
            return PaymentStatus::Failed;
        }

        return PaymentStatus::Pending;
    }
}
