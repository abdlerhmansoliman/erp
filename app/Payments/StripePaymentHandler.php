<?php

namespace App\Payments;

use App\Models\Payment;
use App\Events\PaymentSucceeded;
use App\Events\PaymentFailed;
use App\Events\PaymentCancelled;
use App\Payments\Exceptions\PaymentException;
use App\Payments\Exceptions\PaymentProviderException;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentHandler implements PaymentStrategy
{
    public function __construct()
    {
        // Always use the secret key for server-side operations
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function pay(Payment $payment): array
    {
        try {
            $this->validatePayment($payment);

            $intent = PaymentIntent::create([
                'amount' => $this->convertToCents($payment->amount),
                'currency' => config('services.stripe.currency', 'usd'),
                'description' => "Payment for {$payment->payable_type} #{$payment->payable_id}",
                'metadata' => [
                    'payment_id' => $payment->id,
                    'payable_type' => $payment->payable_type,
                    'payable_id' => $payment->payable_id
                ],
                'payment_method_types' => config('services.stripe.methods', ['card']),
                'statement_descriptor_suffix' => substr(config('app.name'), 0, 22),
                'receipt_email' => $payment->payable->customer->email ?? null,
            ]);

            $payment->transaction_id = $intent->id;
            $payment->provider_response = $intent->toArray();
            $payment->status = PaymentStatus::PROCESSING;
            $payment->save();

            Log::info('Payment intent created', [
                'payment_id' => $payment->id,
                'intent_id' => $intent->id,
                'amount' => $payment->amount
            ]);

            return [
                'client_secret' => $intent->client_secret,
                'payment_id' => $payment->id,
                'publishable_key' => env('STRIPE_KEY') // Use environment variable directly for publishable key
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            
            throw new PaymentProviderException(
                "Payment provider error: {$e->getMessage()}",
                $payment,
                $e->getStripeCode()
            );
        }
    }

    public function confirm(Payment $payment, array $data): array
    {
        try {
            if (PaymentStatus::isFinal($payment->status)) {
                throw new PaymentException('Payment cannot be modified', $payment);
            }

            $intent = PaymentIntent::retrieve($data['payment_intent_id']);
            
            // No need to confirm again as it's already confirmed on the frontend
            if ($data['status'] !== 'succeeded') {
                throw new PaymentException('Payment confirmation failed', $payment);
            }

            $payment->status = $intent->status;
            if ($intent->status === 'succeeded') {
                $payment->payment_date = now();
            }
            $payment->save();

            Log::info('Payment confirmed', [
                'payment_id' => $payment->id,
                'intent_id' => $intent->id,
                'status' => $intent->status
            ]);

            return $payment->toArray();

        } catch (ApiErrorException $e) {
            Log::error('Stripe confirmation error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            throw new PaymentProviderException(
                "Payment confirmation failed: {$e->getMessage()}",
                $payment,
                $e->getStripeCode()
            );
        }
    }

    public function handleWebhook(array $payload): void
    {
        $event = $payload['type'] ?? null;
        $intent = $payload['data']['object'] ?? null;

        if (!$intent) {
            Log::warning('Invalid webhook payload received');
            return;
        }

        $payment = Payment::where('transaction_id', $intent['id'])->first();

        if (!$payment) {
            Log::warning('Payment not found for webhook', ['intent_id' => $intent['id']]);
            return;
        }

        try {
            match($event) {
                'payment_intent.succeeded' => $this->handlePaymentSuccess($payment, $intent),
                'payment_intent.payment_failed' => $this->handlePaymentFailure($payment, $intent),
                'payment_intent.canceled' => $this->handlePaymentCancellation($payment, $intent),
                default => Log::info("Unhandled webhook event: {$event}")
            };
        } catch (\Exception $e) {
            Log::error('Error processing webhook', [
                'payment_id' => $payment->id,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function handlePaymentSuccess(Payment $payment, array $intent): void
    {
        $payment->update([
            'status' => PaymentStatus::SUCCEEDED,
            'payment_date' => now(),
            'provider_response' => $intent
        ]);

        // Trigger any success callbacks/events
        event(new PaymentSucceeded($payment));
    }

    protected function handlePaymentFailure(Payment $payment, array $intent): void
    {
        $payment->update([
            'status' => PaymentStatus::FAILED,
            'provider_response' => $intent
        ]);

        event(new PaymentFailed($payment));
    }

    protected function handlePaymentCancellation(Payment $payment, array $intent): void
    {
        $payment->update([
            'status' => PaymentStatus::CANCELLED,
            'provider_response' => $intent
        ]);

        event(new PaymentCancelled($payment));
    }

    protected function validatePayment(Payment $payment): void
    {
        if ($payment->amount <= 0) {
            throw new PaymentException('Invalid payment amount', $payment);
        }

        if (PaymentStatus::isFinal($payment->status)) {
            throw new PaymentException('Payment cannot be modified', $payment);
        }
    }

    protected function convertToCents(float $amount): int
    {
        return (int) ($amount * 100);
    }
}

