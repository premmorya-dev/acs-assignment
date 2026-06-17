<?php

namespace App\Services;

use App\Models\Customer;

interface WhatsAppClientInterface
{
    public function sendPaymentReminder(Customer $customer): bool;
}
