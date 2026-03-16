<x-mail::message>
# Welcome, {{ $customer->first_name }}!

Thank you for creating an account with us.

You can now browse our catalog, track your orders, and manage your account details.

<x-mail::button :url="config('app.url')">
Visit Our Store
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
