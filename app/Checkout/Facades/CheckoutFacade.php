<?php

declare(strict_types=1);

namespace App\Checkout\Facades;

use App\Checkout\Contracts\CheckoutFacade as CheckoutFacadeContract;
use App\Checkout\DataTransferObjects\CheckoutTotals;
use App\Checkout\DataTransferObjects\PlaceOrderResult;
use App\Checkout\Models\CheckoutSession;
use App\Checkout\Services\CheckoutService;

class CheckoutFacade implements CheckoutFacadeContract
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
    ) {}

    public function startCheckout(int $cartId, ?int $customerId = null): CheckoutSession
    {
        return $this->checkoutService->startCheckout($cartId, $customerId);
    }

    public function setShippingAddress(string $sessionUuid, array $address): CheckoutSession
    {
        return $this->checkoutService->setShippingAddress($sessionUuid, $address);
    }

    public function setBillingAddress(string $sessionUuid, array $address): CheckoutSession
    {
        return $this->checkoutService->setBillingAddress($sessionUuid, $address);
    }

    public function setShippingMethod(string $sessionUuid, string $carrierCode, string $methodCode): CheckoutSession
    {
        return $this->checkoutService->setShippingMethod($sessionUuid, $carrierCode, $methodCode);
    }

    public function setPaymentMethod(string $sessionUuid, string $paymentMethodCode): CheckoutSession
    {
        return $this->checkoutService->setPaymentMethod($sessionUuid, $paymentMethodCode);
    }

    public function applyCoupon(string $sessionUuid, string $couponCode): CheckoutSession
    {
        return $this->checkoutService->applyCoupon($sessionUuid, $couponCode);
    }

    public function removeCoupon(string $sessionUuid): CheckoutSession
    {
        return $this->checkoutService->removeCoupon($sessionUuid);
    }

    public function calculateTotals(string $sessionUuid): CheckoutTotals
    {
        return $this->checkoutService->calculateTotals($sessionUuid);
    }

    public function placeOrder(string $sessionUuid): PlaceOrderResult
    {
        return $this->checkoutService->placeOrder($sessionUuid);
    }

    public function resumeOrder(string $pipelineToken, array $callbackData = []): PlaceOrderResult
    {
        return $this->checkoutService->resumeOrder($pipelineToken, $callbackData);
    }

    public function getSession(string $sessionUuid): ?CheckoutSession
    {
        return $this->checkoutService->getSession($sessionUuid);
    }
}
