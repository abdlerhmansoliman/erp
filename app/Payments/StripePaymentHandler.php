<?php

namespace App\Payments;

use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentHandler implements PaymentStrategy
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function pay(Payment $payment): PaymentIntent
    {
        $intent = PaymentIntent::create([
            'amount' => $payment->amount * 100, 
            'currency' => 'usd',
            'payment_method_types' => ['card'],
            'description' => 'Payment for ' . $payment->payable_type . ' #' . $payment->payable_id,
            'metadata' => [
                'payment_id' => $payment->id,
            ],
        ]);

   
        $payment->transaction_id = $intent->id;
        $payment->status = 'pending';
        $payment->save();

        return $intent;
    }

    public function confirm(string $paymentIntentId): PaymentIntent
    {
        $intent = PaymentIntent::retrieve($paymentIntentId);
        return $intent->confirm(['payment_method' => 'pm_card_visa']); // للاختبار
    }
}

