<?php

namespace App\Services;

use App\Models\CommunicationLog;
use App\Models\Customer;

class ReportService
{
    public function summary(): array
    {
        return [
            'total_customers' => Customer::count(),
            'paid_customers' => Customer::where('payment_status', 'Paid')->count(),
            'pending_customers' => Customer::where('payment_status', 'Pending')->count(),
            'emails_sent' => CommunicationLog::where('type', 'email')->count(),
            'whatsapp_sent' => CommunicationLog::where('type', 'whatsapp')->count(),
        ];
    }
}
