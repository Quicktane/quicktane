<x-mail::message>
# Order Status Update

Hi {{ $order->customer->first_name ?? 'Customer' }},

Your order **#{{ $order->increment_id }}** status has been updated.

- **Previous Status:** {{ ucfirst(str_replace('_', ' ', $fromStatus)) }}
- **New Status:** {{ ucfirst(str_replace('_', ' ', $toStatus)) }}

If you have any questions, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
