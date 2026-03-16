<?php

declare(strict_types=1);

namespace App\Payment\DataTransferObjects;

class PaymentResponse
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly bool $success,
        public readonly ?string $transactionReference = null,
        public readonly ?string $redirectUrl = null,
        public readonly ?string $errorMessage = null,
        public readonly array $metadata = [],
    ) {}
}
