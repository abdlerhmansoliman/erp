<?php
// app/Services/PaymentService.php
namespace App\Services;

use App\Models\Payment;
use App\Payments\PaymentProcessor;

class PaymentService
{
    public function __construct(protected PaymentProcessor $processor) {}

    public function createPayment(array $data): array
    {
        $payment = Payment::create([
            'payable_type' => $data['payable_type'],
            'payable_id' => $data['payable_id'],
            'amount' => $data['amount'],
            'payment_method_id' => $data['payment_method_id'],
            'status' => 'pending',
        ]);

        return $this->processor->pay($payment);
    }

    public function confirmPayment(Payment $payment, array $data): array
    {
        return $this->processor->confirm($payment, $data);
    }
}

