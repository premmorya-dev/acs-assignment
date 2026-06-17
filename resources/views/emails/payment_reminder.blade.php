@component('mail::message')
# Payment Reminder

Hello {{ $customer->name }},

This is a friendly reminder that your payment of **{{ number_format($customer->payment_amount, 2) }}** is still marked as **{{ $customer->payment_status }}**.

Please make the payment as soon as possible.

Thanks,
{{ config('app.name') }}
@endcomponent
