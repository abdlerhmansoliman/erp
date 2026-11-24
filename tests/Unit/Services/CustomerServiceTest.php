<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Services\CustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerService $customerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerService = new CustomerService(new CustomerRepository);
    }

    public function test_customer_service_creates_customer(): void
    {
        $data = [
            'name' => 'Service Test Customer',
            'email' => 'service@example.com',
            'phone' => '0555123456',
            'address' => '789 Business Rd',
        ];

        $customer = $this->customerService->create($data);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Service Test Customer',
            'email' => 'service@example.com',
            'phone' => '0555123456',
            'address' => '789 Business Rd',
        ]);
    }

    public function test_customer_factory_creates_valid_customer(): void
    {
        $customer = Customer::factory()->create();

        $this->assertModelExists($customer);
        $this->assertNotNull($customer->name);
        $this->assertNotNull($customer->email);
    }
}
