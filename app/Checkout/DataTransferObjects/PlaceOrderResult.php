<?php

declare(strict_types=1);

namespace App\Checkout\DataTransferObjects;

use App\Order\Models\Order;

class PlaceOrderResult
{
    /**
     * @param  array<string>  $errors
     */
    public function __construct(
        public readonly bool $success,
        public readonly ?Order $order = null,
        public readonly bool $suspended = false,
        public readonly ?string $pipelineToken = null,
        public readonly ?string $redirectUrl = null,
        public readonly array $errors = [],
    ) {}
}
