<?php

namespace App\Payments;

use App\Models\Transaction;

class CashPaymentHandler implements PaymentStrategy
{
    protected TransactionHandler $handler;

    public function __construct()
    {
        $this->handler = app(TransactionHandler::class);
    }

    public function pay(Transaction $transaction): array
    {
        // Cash payments are completed immediately without external provider
        $this->handler->handleSuccess($transaction, [
            'method' => 'cash',
            'note' => 'Paid in cash',
        ]);

        return [
            'client_secret' => null,
            'transaction_id' => $transaction->id,
        ];
    }

    public function confirm(Transaction $transaction, array $data): Transaction
    {
        // Nothing to confirm for cash; return the fresh transaction
        return $transaction->fresh();
    }

    public function handleWebhook(array $payload): void
    {
        // No webhooks for cash payments
    }
}


