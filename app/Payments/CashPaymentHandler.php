<?php

namespace App\Payments;
use App\Models\Payment;

class CashPaymentHandler implements PaymentStrategy
{
    public function pay(Payment $payment): bool
    {

        $payment->payment_date = now();
        $payment->save();
        return true;
    }
}