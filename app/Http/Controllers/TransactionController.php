<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    /**
     * Create a new transaction and pay it immediately
     */
    public function pay(Request $request): JsonResponse
    {
        $data = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $payment = Payment::findOrFail($data['payment_id']);

        // إنشاء Transaction + الدفع مباشرة
        $result = $this->transactionService->createTransaction( $data);

        return response()->json([
            'success' => true,
            'transaction' => $result['transaction'],
            'payment_result' => $result['payment_result'],
        ]);
    }

    /**
     * Confirm an existing transaction
     */
    public function confirm(Request $request, int $transactionId): JsonResponse
    {
        $data = $request->validate([
            'payment_intent_id' => 'required|string',
            'status' => 'required|string',
        ]);

        $transaction = Transaction::findOrFail($transactionId);

        $transaction = $this->transactionService->confirmTransaction($transaction, $data);

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
        ]);
    }
}
