<x-mail::message>
# Order Confirmation

Hi {{ $order->customer->first_name ?? 'Customer' }},

Thank you for your order **#{{ $order->increment_id }}**.

**Order Summary:**

- **Subtotal:** {{ $order->currency_code }} {{ number_format((float) $order->subtotal, 2) }}
- **Shipping:** {{ $order->currency_code }} {{ number_format((float) $order->shipping_amount, 2) }}
- **Tax:** {{ $order->currency_code }} {{ number_format((float) $order->tax_amount, 2) }}
- **Total:** {{ $order->currency_code }} {{ number_format((float) $order->grand_total, 2) }}

We will notify you when your order has been shipped.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
