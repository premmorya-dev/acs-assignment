<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerCsvUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_csv_and_skip_duplicates(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        Sanctum::actingAs($admin);

        $content = "Name,Phone Number,Email,Payment Amount\n" .
            "Customer One,1234567890,customer1@example.com,100.00\n" .
            "Customer Duplicate,0987654321,customer1@example.com,150.00\n";

        $file = UploadedFile::fake()->createWithContent('customers.csv', $content);

        $response = $this->postJson('/api/admin/upload-csv', [
            'file' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_records' => 2,
                'inserted_records' => 1,
                'duplicate_records' => 1,
            ]);
    }

    public function test_non_admin_cannot_upload_csv(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'password' => bcrypt('password'),
        ]);

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('customers.csv', 100, 'text/csv');

        $response = $this->postJson('/api/admin/upload-csv', [
            'file' => $file,
        ]);

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }
}
