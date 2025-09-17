<?php

namespace App\Payments;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripePaymentHandler
{
    public function pay($payment)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        return PaymentIntent::create([
            'amount' => $payment->amount * 100,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
            'description' => 'Payment for ' . $payment->payable_type . ' #' . $payment->payable_id,
        ]);
    }

public function confirm($paymentIntentId)
{
    Stripe::setApiKey(env('STRIPE_SECRET'));

    // أولاً: نجلب الـ PaymentIntent الموجود فعلاً من Stripe
    $intent = PaymentIntent::retrieve($paymentIntentId);

    // ثانياً: نعمل تأكيد له ونربطه ببطاقة اختبار جاهزة
    return $intent->confirm([
        'payment_method' => 'pm_card_visa',
    ]);
}
}
