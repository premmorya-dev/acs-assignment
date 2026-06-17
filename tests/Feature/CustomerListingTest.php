<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_listing_returns_pagination_and_filters(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        Customer::factory()->create([
            'name' => 'Alice Example',
            'email' => 'alice@example.com',
            'phone_number' => '1111111111',
        ]);

        Customer::factory()->create([
            'name' => 'Bob Example',
            'email' => 'bob@example.com',
            'phone_number' => '2222222222',
        ]);

        Customer::factory(15)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/customers?name=Alice&per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                'links' => ['first', 'last', 'prev', 'next'],
            ])
            ->assertJsonCount(1, 'data');
    }
}
