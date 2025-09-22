<?php

namespace Tests\Unit\Payments;

use Tests\TestCase;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Payments\PaymentProcessor;
use App\Payments\StripePaymentHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentProcessorTest extends TestCase
{
    use RefreshDatabase;

    protected $processor;
    protected $payment;
    protected $stripeHandler;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->processor = new PaymentProcessor();
        
        $method = PaymentMethod::create([
            'name' => 'stripe',
            'config' => json_encode([
                'secret' => 'test_key',
                'publishable' => 'test_pub_key'
            ])
        ]);

        $this->payment = Payment::create([
            'payable_type' => 'App\\Models\\SalesInvoice',
            'payable_id' => 1,
            'amount' => 100.00,
            'payment_method_id' => $method->id,
            'status' => 'pending'
        ]);

        $this->stripeHandler = $this->createMock(StripePaymentHandler::class);
    }

    /** @test */
    public function it_resolves_correct_payment_handler()
    {
        $this->stripeHandler
            ->expects($this->once())
            ->method('pay')
            ->with($this->payment)
            ->willReturn(['status' => 'success']);

        app()->instance(StripePaymentHandler::class, $this->stripeHandler);

        $result = $this->processor->pay($this->payment);
        
        $this->assertEquals(['status' => 'success'], $result);
    }

    /** @test */
    public function it_throws_exception_for_unsupported_method()
    {
        $this->payment->paymentMethod->update(['name' => 'unsupported']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported payment method: unsupported');

        $this->processor->pay($this->payment);
    }
}