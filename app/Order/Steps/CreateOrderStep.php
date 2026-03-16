<?php

declare(strict_types=1);

namespace App\Order\Steps;

use App\Order\Contracts\OrderFacade;
use Closure;
use Quicktane\Core\Pipeline\PipelineContext;
use Quicktane\Core\Pipeline\PipelineStep;

class CreateOrderStep implements PipelineStep
{
    public function __construct(
        private readonly OrderFacade $orderFacade,
    ) {}

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $order = $this->orderFacade->createOrder([
            'store_id' => (int) $context->get('store_id'),
            'customer_id' => $context->get('customer_id'),
            'customer_email' => (string) $context->get('customer_email'),
            'customer_group_id' => $context->get('customer_group_id'),
            'subtotal' => (string) $context->get('subtotal'),
            'shipping_amount' => (string) $context->get('shipping_amount', '0.0000'),
            'discount_amount' => (string) $context->get('discount_amount', '0.0000'),
            'tax_amount' => (string) $context->get('tax_amount', '0.0000'),
            'grand_total' => (string) $context->get('grand_total'),
            'currency_code' => (string) $context->get('currency_code'),
            'shipping_method_code' => $context->get('shipping_method_code'),
            'shipping_method_label' => $context->get('shipping_method_label'),
            'payment_method_code' => $context->get('payment_method_code'),
            'payment_method_label' => $context->get('payment_method_label'),
            'coupon_code' => $context->get('coupon_code'),
            'total_quantity' => (int) $context->get('total_quantity'),
            'weight' => $context->get('total_weight'),
            'customer_note' => $context->get('customer_note'),
            'ip_address' => $context->get('ip_address'),
            'items' => (array) $context->get('order_items', []),
        ]);

        $context->set('order_id', $order->id);
        $context->set('order_uuid', $order->uuid);
        $context->set('order_increment_id', $order->increment_id);

        return $next($context);
    }

    public function compensate(PipelineContext $context): void
    {
        $orderId = $context->get('order_id');

        if ($orderId !== null) {
            $order = $this->orderFacade->getOrder((int) $orderId);
            $order?->delete();
        }
    }

    public static function priority(): int
    {
        return 500;
    }

    public static function pipeline(): string
    {
        return 'checkout.place';
    }
}
