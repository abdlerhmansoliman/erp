<?php

// app/Payments/StripePaymentHandler.php
namespace App\Payments;

use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentHandler implements PaymentStrategy
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function pay(Payment $payment): array
    {
$intent = PaymentIntent::create([
    'amount' => intval($payment->amount * 100),
    'currency' => 'usd',
    'description' => "Payment for {$payment->payable_type} #{$payment->payable_id}",
    'metadata' => ['payment_id' => $payment->id],
    'payment_method_types' => ['card'], // فقط
]);

        $payment->transaction_id = $intent->id;
        $payment->provider_response = $intent->toArray();
        $payment->save();

        return [
            'client_secret' => $intent->client_secret,
            'payment_id' => $payment->id,
        ];
    }

    public function confirm(Payment $payment, array $data): array
    {
        $intent = PaymentIntent::retrieve($payment->transaction_id);
        $intent->confirm([
            'payment_method' => $data['payment_method'] ?? null,
        ]);

        $payment->status = $intent->status;
        $payment->provider_response = $intent->toArray();
        $payment->save();

        return $payment->toArray();
    }

public function handleWebhook(array $payload): void
{

    $event = $payload['type'] ?? null;

    if ($event === 'payment_intent.succeeded') {
        $intent = $payload['data']['object'];
        $payment = Payment::where('transaction_id', $intent['id'])->first();

        if ($payment) {
            $payment->status = 'succeeded';
            $payment->save();
        }
    }

    if ($event === 'payment_intent.payment_failed') {
        $intent = $payload['data']['object'];
        $payment = Payment::where('transaction_id', $intent['id'])->first();

        if ($payment) {
            $payment->status = 'failed';
            $payment->save();
        }
    }
}

}

