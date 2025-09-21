<?php

namespace App\Payments;

use App\Models\Payment;
use App\Payments\Handlers\CardPaymentHandler;

class PaymentProcessor
{
    protected $strategies = [];

    public function __construct()
    {
        $this->strategies = [
            'Cash' => CashPaymentHandler::class,
            'Bank' => BankPaymentHandler::class,
            'Stripe' => StripePaymentHandler::class,
        ];
    }

    public function process(Payment $payment): mixed
    {
        $methodName = $payment->paymentMethod->name;

        if (!isset($this->strategies[$methodName])) {
            throw new \Exception("No handler for payment method: {$methodName}");
        }

        $strategy = new $this->strategies[$methodName]();
        return $strategy->pay($payment);
    }
}
