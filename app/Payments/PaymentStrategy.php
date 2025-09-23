<?php


namespace App\Payments;

use App\Models\Payment;
use App\Models\Transaction;

interface PaymentStrategy
{
    public function pay(Transaction $transaction): array;
    public function confirm(Transaction $transaction, array $data): Transaction;
    public function handleWebhook(array $payload): void;
}