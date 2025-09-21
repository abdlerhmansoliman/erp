<?php


namespace App\Payments;

use App\Models\Payment;

interface PaymentStrategy
{
    public function pay(Payment $payment): array;
    public function confirm(Payment $payment, array $data): array;
    public function handleWebhook(array $payload): void;
}