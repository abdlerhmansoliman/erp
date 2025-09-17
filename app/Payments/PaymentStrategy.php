<?php


namespace App\Payments;

use App\Models\Payment;

interface PaymentStrategy
{
    public function pay(Payment $payment): bool;
}