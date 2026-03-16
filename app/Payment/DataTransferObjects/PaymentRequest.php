<?php

declare(strict_types=1);

namespace App\Payment\DataTransferObjects;

class PaymentRequest
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly string $amount,
        public readonly string $currencyCode,
        public readonly ?int $orderId = null,
        public readonly ?string $customerEmail = null,
        public readonly ?string $returnUrl = null,
        public readonly array $metadata = [],
    ) {}
}
