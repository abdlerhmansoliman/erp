<?php

namespace App\Payments;

use App\Models\Transaction;

use App\Payments\Exceptions\PaymentException;
use App\Payments\Exceptions\PaymentProviderException;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentHandler implements PaymentStrategy
{
    protected TransactionHandler $handler;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->handler = app(TransactionHandler::class);
    }

    public function pay(Transaction $transaction): array
    {
        try {
        
            $this->validateTransaction($transaction);

            $intent = PaymentIntent::create([
                'amount' => $this->convertToCents($transaction->amount),
                'currency' => config('services.stripe.currency', 'usd'),
                'description' => "Payment for sales",
                'metadata' => ['transaction_id' => $transaction->id],
                'payment_method_types' => config('services.stripe.methods', ['card']),
                'statement_descriptor_suffix' => substr(config('app.name'), 0, 22),
            ]);

            $transaction->update([
                'transaction_id' => $intent->id,
                'provider_response' => $intent->toArray(),
                'status' => PaymentStatus::PROCESSING,
            ]);

            return [
                'client_secret' => $intent->client_secret,
                'transaction_id' => $transaction->id,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error', ['transaction_id' => $transaction->id, 'error' => $e->getMessage()]);
            throw new PaymentProviderException(
                "Payment provider error: {$e->getMessage()}",
                $transaction->payment,
                $e->getStripeCode()
            );
        }
    }

public function confirm(Transaction $transaction, array $data): Transaction
{
    try {
        if (PaymentStatus::isFinal($transaction->status)) {
            throw new PaymentException('Transaction cannot be modified', $transaction);
        }

        $intent = PaymentIntent::retrieve($data['payment_intent_id']);

        if ($data['status'] !== 'succeeded') {
            throw new PaymentException('Payment confirmation failed', $transaction);
        }

        $transaction->update([
            'status' => PaymentStatus::SUCCEEDED,
            'paid_at' => now(),
            'provider_response' => $intent->toArray(),
        ]);

        // تحديث حالة الـ Payment بناءً على مجموع Transactions
        $totalPaid = $transaction->payment->transactions()->sum('amount');
        $transaction->payment->update([
            'status' => $totalPaid >= $transaction->payment->amount
                ? PaymentStatus::SUCCEEDED
                : PaymentStatus::PARTIAL,
        ]);

        return $transaction->fresh(); 
    } catch (ApiErrorException $e) {
        Log::error('Stripe confirmation error', [
            'transaction_id' => $transaction->id,
            'error' => $e->getMessage()
        ]);

        throw new PaymentProviderException(
            "Payment confirmation failed: {$e->getMessage()}",
            $transaction->payment,
            $e->getStripeCode()
        );
    }
}


        public function handleWebhook(array $payload): void
        {
            $event = $payload['type'] ?? null;
            $intent = $payload['data']['object'] ?? null;

            if (!$intent) return;

            $transaction = Transaction::where('transaction_id', $intent['id'])->first();
            if (!$transaction) return;

            match($event) {
                'payment_intent.succeeded' => $this->handler->handleSuccess($transaction, $intent),
                'payment_intent.payment_failed' => $this->handler->handleFailure($transaction, $intent),
                'payment_intent.canceled' => $this->handler->handleCancellation($transaction, $intent),
                default => null,
            };
        }
    protected function validateTransaction(Transaction $transaction): void
    {
        if ($transaction->amount <= 0) {
            throw new PaymentException('Invalid transaction amount', $transaction);
        }

        if (PaymentStatus::isFinal($transaction->status)) {
            throw new PaymentException('Transaction cannot be modified', $transaction);
        }
    }

    protected function convertToCents(float $amount): int
    {
        return (int) ($amount * 100);
    }
}
