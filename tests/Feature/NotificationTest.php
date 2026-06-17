<?php

namespace Tests\Feature;

use App\Jobs\SendCustomerNotificationJob;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_notification_dispatches_job_and_returns_report(): void
    {
        Mail::fake();
        Http::fake();
        Queue::fake();

        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $customer = Customer::factory()->create([
            'payment_status' => 'Pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/customer/{$customer->id}/send-notification", [
            'type' => 'email',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'report' => [
                    'total_customers',
                    'paid_customers',
                    'pending_customers',
                    'emails_sent',
                    'whatsapp_sent',
                ],
            ])
            ->assertJson(['message' => 'Notification sent successfully']);

        Queue::assertPushed(SendCustomerNotificationJob::class);
    }

    public function test_report_summary_endpoint_returns_counts(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        Customer::factory()->count(3)->create(['payment_status' => 'Pending']);
        Customer::factory()->count(2)->create(['payment_status' => 'Paid']);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/reports/summary');

        $response->assertStatus(200)
            ->assertJson([
                'report' => [
                    'total_customers' => 5,
                    'paid_customers' => 2,
                    'pending_customers' => 3,
                ],
            ]);
    }
}
