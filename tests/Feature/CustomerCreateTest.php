<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_customer(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main Street',
        ];

        $response = $this->postJson('/api/customers', $payload);

        $response->assertUnauthorized();
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_authenticated_user_can_create_customer_with_valid_data(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'phone' => '0123456789',
            'address' => '456 Market Avenue',
        ];

        $response = $this->postJson('/api/customers', $payload);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'phone',
                'address',
            ],
        ]);
        $response->assertJson([
            'data' => [
                'name' => 'Alice Smith',
                'email' => 'alice@example.com',
                'phone' => '0123456789',
                'address' => '456 Market Avenue',
            ],
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'phone' => '0123456789',
            'address' => '456 Market Avenue',
        ]);
    }

    public function test_customer_creation_fails_when_name_is_missing(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'email' => 'noname@example.com',
            'phone' => '1234567890',
            'address' => 'Some address',
        ];

        $response = $this->postJson('/api/customers', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_customer_creation_fails_when_name_is_empty(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => '',
            'email' => 'emptyname@example.com',
        ];

        $response = $this->postJson('/api/customers', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_customer_creation_fails_when_name_exceeds_max_length(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => str_repeat('a', 256),
            'email' => 'toolongname@example.com',
        ];

        $response = $this->postJson('/api/customers', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_customer_creation_fails_when_email_format_is_invalid(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Invalid Email Customer',
            'email' => 'not-a-valid-email',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/customers', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_customer_creation_fails_when_phone_exceeds_max_length(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'phone' => str_repeat('1', 21),
        ];

        $response = $this->postJson('/api/customers', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['phone']);
    }

    public function test_customer_creation_fails_when_address_exceeds_max_length(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'address' => str_repeat('x', 256),
        ];

        $response = $this->postJson('/api/customers', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['address']);
    }
}
