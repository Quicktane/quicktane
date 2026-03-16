<?php

declare(strict_types=1);

namespace App\Payment\Steps;

use App\Payment\Contracts\PaymentFacade;
use App\Payment\DataTransferObjects\PaymentRequest;
use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;
use Quicktane\Core\Pipeline\PipelineSuspendException;
use RuntimeException;

class AuthorizePaymentStep implements PipelineStep
{
    public function __construct(
        private readonly PaymentFacade $paymentFacade,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        if ($context->get('payment_confirmed') === true) {
            $transactionId = $context->get('transaction_id');

            if ($transactionId === null) {
                throw new RuntimeException('Transaction ID is missing after payment confirmation.');
            }

            $transaction = $this->paymentFacade->getTransaction($transactionId);

            if ($transaction === null) {
                throw new RuntimeException("Transaction not found: {$transactionId}");
            }

            return $next($context);
        }

        $paymentMethodCode = $context->get('payment_method_code');

        if ($paymentMethodCode === null) {
            throw new RuntimeException('Payment method code is required.');
        }

        $paymentRequest = new PaymentRequest(
            amount: (string) $context->get('grand_total'),
            currencyCode: (string) $context->get('currency_code'),
            orderId: $context->get('order_id'),
            customerEmail: $context->get('customer_email'),
            returnUrl: $context->get('return_url'),
            metadata: $context->get('payment_metadata', []),
        );

        $paymentResponse = $this->paymentFacade->authorize($paymentMethodCode, $paymentRequest);

        if ($paymentResponse->redirectUrl !== null) {
            $context->set('transaction_reference', $paymentResponse->transactionReference);

            throw new PipelineSuspendException(
                redirectUrl: $paymentResponse->redirectUrl,
                reason: 'payment_redirect',
                metadata: [
                    'transaction_reference' => $paymentResponse->transactionReference,
                ],
            );
        }

        if (! $paymentResponse->success) {
            throw new RuntimeException(
                'Payment authorization failed: '.($paymentResponse->errorMessage ?? 'Unknown error'),
            );
        }

        $transaction = $this->paymentFacade->getTransactionByReference($paymentResponse->transactionReference);

        $context->set('transaction_id', $transaction?->id);
        $context->set('payment_reference', $paymentResponse->transactionReference);

        return $next($context);
    }

    public function compensate(PipelineContext $context): void
    {
        $transactionId = $context->get('transaction_id');

        if ($transactionId !== null) {
            $this->paymentFacade->void($transactionId);
        }
    }

    public static function priority(): int
    {
        return 700;
    }

    public static function pipeline(): string
    {
        return 'checkout.place';
    }
}
