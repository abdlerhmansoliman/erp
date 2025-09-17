<?php

namespace App\Payments\Handlers;

use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CardPaymentHandler
{
    public function pay(Payment $payment): bool
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => $payment->amount * 100, // بالمليم
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        return isset($intent->id);
    }
}
