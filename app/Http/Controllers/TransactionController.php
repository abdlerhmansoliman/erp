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

        $payment = Payment::with('transactions')->findOrFail($data['payment_id']);

        // Prevent paying more than due
        $totalSucceeded = $payment->transactions()
            ->where('status', 'succeeded')
            ->sum('amount');
        $remaining = max(0, ($payment->amount ?? 0) - $totalSucceeded);

        if ($remaining <= 0 || ($payment->status === 'succeeded')) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already completed.'
            ], 409);
        }

        if ($data['amount'] > $remaining) {
            return response()->json([
                'success' => false,
                'message' => 'Requested amount exceeds remaining balance.',
                'remaining' => $remaining
            ], 409);
        }

        $result = $this->transactionService->createTransaction($data);

        return response()->json([
            'success' => true,
            'client_secret' => $result['client_secret'],
            'transaction_id' => $result['transaction_id'],
        ]);
    }

    /**
     * Get transaction details
     */
    public function show(int $transactionId): JsonResponse
    {
        $transaction = Transaction::with('payment')->findOrFail($transactionId);
        return response()->json($transaction);
    }

    /**
     * List available payment methods
     */
    public function methods(): JsonResponse
    {
        return response()->json(\App\Models\PaymentMethod::all());
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
