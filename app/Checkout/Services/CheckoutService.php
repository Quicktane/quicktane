<?php

declare(strict_types=1);

namespace App\Checkout\Services;

use App\Cart\Contracts\CartFacade;
use App\Checkout\DataTransferObjects\CheckoutTotals;
use App\Checkout\DataTransferObjects\PlaceOrderResult;
use App\Checkout\Models\CheckoutSession;
use App\Checkout\Repositories\CheckoutSessionRepository;
use App\Customer\Models\Customer;
use App\Order\Contracts\OrderFacade;
use Illuminate\Validation\ValidationException;
use Quicktane\Core\Pipeline\Pipeline;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Shipping\Contracts\ShippingFacade;

class CheckoutService
{
    public function __construct(
        private readonly CheckoutSessionRepository $checkoutSessionRepository,
        private readonly CartFacade $cartFacade,
        private readonly ShippingFacade $shippingFacade,
        private readonly OrderFacade $orderFacade,
        private readonly Pipeline $pipeline,
    ) {}

    public function startCheckout(int $cartId, ?int $customerId = null): CheckoutSession
    {
        $cart = $this->cartFacade->getCartWithItems($cartId);

        if ($cart === null || $cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => ['Cart is empty or not found.'],
            ]);
        }

        $existingSession = $this->checkoutSessionRepository->findByCart($cartId);

        if ($existingSession !== null) {
            return $existingSession;
        }

        return $this->checkoutSessionRepository->create([
            'cart_id' => $cartId,
            'customer_id' => $customerId,
            'expires_at' => now()->addHours(2),
        ]);
    }

    /**
     * @param  array<string, mixed>  $address
     */
    public function setShippingAddress(string $sessionUuid, array $address): CheckoutSession
    {
        $session = $this->getSessionOrFail($sessionUuid);

        return $this->checkoutSessionRepository->update($session, [
            'shipping_address' => $address,
            'step' => 'shipping_address',
        ]);
    }

    /**
     * @param  array<string, mixed>  $address
     */
    public function setBillingAddress(string $sessionUuid, array $address): CheckoutSession
    {
        $session = $this->getSessionOrFail($sessionUuid);

        return $this->checkoutSessionRepository->update($session, [
            'billing_address' => $address,
            'step' => 'billing_address',
        ]);
    }

    public function setShippingMethod(string $sessionUuid, string $carrierCode, string $methodCode): CheckoutSession
    {
        $session = $this->getSessionOrFail($sessionUuid);

        $shippingMethod = $this->shippingFacade->getMethod($methodCode);
        $label = $shippingMethod?->name ?? "{$carrierCode} - {$methodCode}";

        return $this->checkoutSessionRepository->update($session, [
            'shipping_method_code' => "{$carrierCode}_{$methodCode}",
            'shipping_method_label' => $label,
            'step' => 'shipping_method',
        ]);
    }

    public function setPaymentMethod(string $sessionUuid, string $paymentMethodCode): CheckoutSession
    {
        $session = $this->getSessionOrFail($sessionUuid);

        return $this->checkoutSessionRepository->update($session, [
            'payment_method_code' => $paymentMethodCode,
            'step' => 'payment_method',
        ]);
    }

    public function applyCoupon(string $sessionUuid, string $couponCode): CheckoutSession
    {
        $session = $this->getSessionOrFail($sessionUuid);

        return $this->checkoutSessionRepository->update($session, [
            'coupon_code' => $couponCode,
        ]);
    }

    public function removeCoupon(string $sessionUuid): CheckoutSession
    {
        $session = $this->getSessionOrFail($sessionUuid);

        return $this->checkoutSessionRepository->update($session, [
            'coupon_code' => null,
        ]);
    }

    public function calculateTotals(string $sessionUuid): CheckoutTotals
    {
        $session = $this->getSessionOrFail($sessionUuid);

        $context = $this->buildPipelineContext($session);

        $result = $this->pipeline->run('checkout.totals', $context);

        $totals = new CheckoutTotals(
            subtotal: (string) ($result->data['subtotal'] ?? '0.0000'),
            shippingAmount: (string) ($result->data['shipping_amount'] ?? '0.0000'),
            discountAmount: (string) ($result->data['discount_amount'] ?? '0.0000'),
            taxAmount: (string) ($result->data['tax_amount'] ?? '0.0000'),
            grandTotal: (string) ($result->data['grand_total'] ?? '0.0000'),
            breakdown: [
                'tax_details' => $result->data['tax_details'] ?? [],
                'discount_details' => $result->data['discount_details'] ?? [],
            ],
            freeShipping: (bool) ($result->data['free_shipping'] ?? false),
        );

        $this->checkoutSessionRepository->update($session, [
            'totals' => [
                'subtotal' => $totals->subtotal,
                'shipping_amount' => $totals->shippingAmount,
                'discount_amount' => $totals->discountAmount,
                'tax_amount' => $totals->taxAmount,
                'grand_total' => $totals->grandTotal,
            ],
            'shipping_amount' => $totals->shippingAmount,
        ]);

        return $totals;
    }

    public function placeOrder(string $sessionUuid): PlaceOrderResult
    {
        $session = $this->getSessionOrFail($sessionUuid);

        $context = $this->buildPipelineContext($session);

        $cart = $this->cartFacade->getCartWithItems($session->cart_id);

        $orderItems = [];

        foreach ($cart->items as $item) {
            $orderItems[] = [
                'product_id' => $item->product_id,
                'product_uuid' => $item->product_uuid,
                'product_type' => $item->product_type,
                'sku' => $item->sku,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'row_total' => $item->row_total,
                'weight' => $item->weight ?? null,
                'options' => $item->options,
            ];
        }

        $context->set('order_items', $orderItems);

        if ($session->customer_id !== null) {
            $customer = Customer::query()->find($session->customer_id);

            if ($customer !== null) {
                $context->set('customer_email', $customer->email);
                $context->set('customer_group_id', $customer->customer_group_id);
            }
        }

        $result = $this->pipeline->run('checkout.place', $context);

        if ($result->isSuspended) {
            return new PlaceOrderResult(
                success: false,
                suspended: true,
                pipelineToken: $result->token,
                redirectUrl: $result->redirectUrl,
            );
        }

        $orderId = $result->data['order_id'] ?? null;
        $order = $orderId !== null ? $this->orderFacade->getOrder((int) $orderId) : null;

        $this->checkoutSessionRepository->delete($session);

        return new PlaceOrderResult(
            success: true,
            order: $order,
        );
    }

    /**
     * @param  array<string, mixed>  $callbackData
     */
    public function resumeOrder(string $pipelineToken, array $callbackData = []): PlaceOrderResult
    {
        $result = $this->pipeline->resume($pipelineToken, $callbackData);

        if ($result->isSuspended) {
            return new PlaceOrderResult(
                success: false,
                suspended: true,
                pipelineToken: $result->token,
                redirectUrl: $result->redirectUrl,
            );
        }

        $orderId = $result->data['order_id'] ?? null;
        $order = $orderId !== null ? $this->orderFacade->getOrder((int) $orderId) : null;

        return new PlaceOrderResult(
            success: true,
            order: $order,
        );
    }

    public function getSession(string $sessionUuid): ?CheckoutSession
    {
        return $this->checkoutSessionRepository->findByUuid($sessionUuid);
    }

    private function getSessionOrFail(string $sessionUuid): CheckoutSession
    {
        $session = $this->checkoutSessionRepository->findByUuid($sessionUuid);

        if ($session === null) {
            throw ValidationException::withMessages([
                'session' => ['Checkout session not found.'],
            ]);
        }

        return $session;
    }

    private function buildPipelineContext(CheckoutSession $session): PipelineContext
    {
        $context = new PipelineContext;
        $context->set('cart_id', $session->cart_id);
        $context->set('customer_id', $session->customer_id);
        $context->set('shipping_address', $session->shipping_address ?? []);
        $context->set('billing_address', $session->billing_address ?? []);
        $context->set('shipping_method_code', $session->shipping_method_code);
        $context->set('shipping_method_label', $session->shipping_method_label);
        $context->set('payment_method_code', $session->payment_method_code);
        $context->set('coupon_code', $session->coupon_code);

        return $context;
    }
}
