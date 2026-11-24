<?php

namespace App\Payments;

use App\Events\PaymentCancelled;
use App\Events\PaymentFailed;
use App\Events\PaymentSucceeded;
use App\Models\Transaction;

class TransactionHandler
{
    public function handleSuccess(Transaction $transaction, array $providerResponse): void
    {
        $transaction->update([
            'status' => PaymentStatus::SUCCEEDED,
            'paid_at' => now(),
            'provider_response' => $providerResponse,
        ]);

        $transaction->payment->update([
            'status' => $transaction->payment->transactions()->sum('amount') >= $transaction->payment->amount
                ? PaymentStatus::SUCCEEDED
                : PaymentStatus::PARTIAL,
        ]);

        event(new PaymentSucceeded($transaction->payment));
    }

    public function handleFailure(Transaction $transaction, array $providerResponse): void
    {
        $transaction->update([
            'status' => PaymentStatus::FAILED,
            'provider_response' => $providerResponse,
        ]);

        event(new PaymentFailed($transaction->payment));
    }

    public function handleCancellation(Transaction $transaction, array $providerResponse): void
    {
        $transaction->update([
            'status' => PaymentStatus::CANCELLED,
            'provider_response' => $providerResponse,
        ]);

        event(new PaymentCancelled($transaction->payment));
    }
}
