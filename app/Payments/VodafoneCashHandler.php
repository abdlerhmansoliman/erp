<?php
// app/Payments/VodafoneCashHandler.php
namespace App\Payments;

use App\Models\Payment;

class VodafoneCashHandler implements PaymentStrategy
{
    public function pay(Payment $payment): bool
    {
        // محاكاة دفع Vodafone Cash
        $payment->payment_date = now();
        $payment->save();
        return true;
    }
}
