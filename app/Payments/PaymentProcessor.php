<?php

namespace App\Payments;

use App\Models\Transaction;

class PaymentProcessor
{
    protected array $strategies = [
        'stripe' => StripePaymentHandler::class,
        // cash/bank...
    ];

protected function resolveHandler(Transaction $transaction): PaymentStrategy
{
    if (!$transaction->paymentMethod) {
        throw new \Exception("Transaction {$transaction->id} has no payment method assigned.");
    }

    $methodName = strtolower($transaction->paymentMethod->name);
    $handlerClass = $this->strategies[$methodName] ?? null;

    if (!$handlerClass) {
        throw new \Exception("Unsupported payment method: {$methodName}");
    }

    return app($handlerClass);
}


    public function pay(Transaction $transaction): array
    {
        

        return $this->resolveHandler($transaction)->pay($transaction);
    }

    public function confirm(Transaction $transaction, array $data): Transaction
    {
        return $this->resolveHandler($transaction)->confirm($transaction, $data);
    }

    public function webhook(string $methodName, array $payload): void
    {
        $handlerClass = $this->strategies[strtolower($methodName)] ?? null;
        if ($handlerClass) {
            app($handlerClass)->handleWebhook($payload);
        }
    }
}
