<?php

declare(strict_types=1);

namespace App\Payment\Services;

use App\Payment\Contracts\PaymentGateway;

class PaymentGatewayRegistry
{
    /** @var array<string, PaymentGateway> */
    private array $gateways = [];

    public function register(PaymentGateway $gateway): void
    {
        $this->gateways[$gateway->code()] = $gateway;
    }

    public function getGateway(string $code): ?PaymentGateway
    {
        return $this->gateways[$code] ?? null;
    }

    /**
     * @return array<string, PaymentGateway>
     */
    public function getGateways(): array
    {
        return $this->gateways;
    }
}
