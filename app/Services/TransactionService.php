<?php
// app/Services/TransactionService.php
namespace App\Services;

use App\Models\Payment;
use App\Models\Transaction;
use App\Payments\PaymentProcessor;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function __construct(protected PaymentProcessor $processor) {}

    /**
     * Create a new transaction for a given payment
     */
public function createTransaction(array $data)
{
    // جلب الـ Payment
    $payment = Payment::with('transactions')->findOrFail($data['payment_id']);

    // Guard against overpayment (double-check at service level)
    $totalSucceeded = $payment->transactions()
        ->where('status', 'succeeded')
        ->sum('amount');
    $remaining = max(0, ($payment->amount ?? 0) - $totalSucceeded);
    if ($remaining <= 0) {
        throw new \RuntimeException('Payment already completed.');
    }
    if ($data['amount'] > $remaining) {
        throw new \RuntimeException('Requested amount exceeds remaining balance.');
    }

    // إنشاء Transaction مرتبط بالـ Payment
    $transaction = $payment->transactions()->create([
        'amount' => $data['amount'],
        'payment_method_id' => $data['payment_method_id'],
        'status' => 'pending',
    ]);

    // تحميل العلاقة قبل الدفع
    $transaction->load('paymentMethod');

    // معالجة الدفع مباشرة
    $paymentResult = $this->processor->pay($transaction);

    return [
        'client_secret' => $paymentResult['client_secret'] ?? null,
        'transaction_id' => $transaction->id,
    ];
}

    /**
     * Pay an existing transaction
     */
    public function payTransaction(Transaction $transaction): array
    {

        return $this->processor->pay($transaction);
    }

    /**
     * Confirm an existing transaction
     */
    public function confirmTransaction(Transaction $transaction, array $data): Transaction
    {
        Log::info('Confirming transaction', ['transaction_id' => $transaction->id]);

        return $this->processor->confirm($transaction, $data);
    }
}
