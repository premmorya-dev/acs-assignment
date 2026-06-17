<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_payment_status_can_be_updated(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $customer = Customer::factory()->create([
            'payment_status' => 'Pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/customer/{$customer->id}/payment-status", [
            'payment_status' => 'Paid',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $customer->id)
            ->assertJsonPath('data.payment_status', 'Paid');
    }

    public function test_invalid_payment_status_returns_validation_error(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $customer = Customer::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/customer/{$customer->id}/payment-status", [
            'payment_status' => 'Late',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'errors']);
    }
}
