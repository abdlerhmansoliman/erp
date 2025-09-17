<?php
// app/Payments/BankPaymentHandler.php
namespace App\Payments;

use App\Models\Payment;

class BankPaymentHandler implements PaymentStrategy
{
    public function pay(Payment $payment): bool
    {
        // محاكاة تحويل بنكي
        $success = rand(0,1) === 1;

        if ($success) {
            $payment->payment_date = now();
            $payment->save();
        }

        return $success;
    }
}
