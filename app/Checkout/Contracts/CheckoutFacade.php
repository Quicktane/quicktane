<?php

declare(strict_types=1);

namespace App\Checkout\Contracts;

use App\Checkout\DataTransferObjects\CheckoutTotals;
use App\Checkout\DataTransferObjects\PlaceOrderResult;
use App\Checkout\Models\CheckoutSession;

interface CheckoutFacade
{
    public function startCheckout(int $cartId, ?int $customerId = null): CheckoutSession;

    /**
     * @param  array<string, mixed>  $address
     */
    public function setShippingAddress(string $sessionUuid, array $address): CheckoutSession;

    /**
     * @param  array<string, mixed>  $address
     */
    public function setBillingAddress(string $sessionUuid, array $address): CheckoutSession;

    public function setShippingMethod(string $sessionUuid, string $carrierCode, string $methodCode): CheckoutSession;

    public function setPaymentMethod(string $sessionUuid, string $paymentMethodCode): CheckoutSession;

    public function applyCoupon(string $sessionUuid, string $couponCode): CheckoutSession;

    public function removeCoupon(string $sessionUuid): CheckoutSession;

    public function calculateTotals(string $sessionUuid): CheckoutTotals;

    public function placeOrder(string $sessionUuid): PlaceOrderResult;

    /**
     * @param  array<string, mixed>  $callbackData
     */
    public function resumeOrder(string $pipelineToken, array $callbackData = []): PlaceOrderResult;

    public function getSession(string $sessionUuid): ?CheckoutSession;
}
