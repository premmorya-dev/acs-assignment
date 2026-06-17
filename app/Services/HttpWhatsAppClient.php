<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class HttpWhatsAppClient implements WhatsAppClientInterface
{
    public function sendPaymentReminder(Customer $customer): bool
    {
        $url = Config::get('services.whatsapp.url');
        $token = Config::get('services.whatsapp.token');

        if (! $url || ! $token) {
            return false;
        }

        Http::withToken($token)
            ->acceptJson()
            ->post($url, [
                'to' => $customer->phone_number,
                'message' => "Hello {$customer->name}, your payment of {$customer->payment_amount} is still pending. Please pay as soon as possible.",
                'type' => 'payment_reminder',
            ]);

        return true;
    }
}
