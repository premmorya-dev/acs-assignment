<?php

namespace App\Jobs;

use App\Models\CommunicationLog;
use App\Models\Customer;
use App\Models\User;
use App\Mail\PaymentReminderMail;
use App\Services\WhatsAppClientInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCustomerNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Customer $customer;

    private User $user;

    private string $type;

    public function __construct(Customer $customer, User $user, string $type)
    {
        $this->customer = $customer;
        $this->user = $user;
        $this->type = $type;
    }

    public function handle(WhatsAppClientInterface $whatsappClient): void
    {
        if ($this->type === 'email') {
            // Call email service to send message using dependency injection
           // Mail::to($this->customer->email)->send(new PaymentReminderMail($this->customer));
        } else {
            // Call whatsapp service to send message using dependency injection
            //  $whatsappClient->sendPaymentReminder($this->customer);
        }

        CommunicationLog::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'type' => $this->type,
            'sent_at' => now(),
        ]);
    }
}
