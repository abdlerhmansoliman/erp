<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SalesInvoice;
use App\Payments\StripePaymentHandler;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
public function payWithStripe(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
        'payable_type' => 'required|string', 
        'payable_id' => 'required|integer',
        'payment_method_id' => 'required|integer', 
    ]);

    $payment = Payment::create([
        'payable_type' => $request->payable_type,
        'payable_id' => $request->payable_id,
        'amount' => $request->amount,
        'payment_method_id' => $request->payment_method_id,
    ]);

    $handler = new StripePaymentHandler();
    $intent = $handler->pay($payment);

    return response()->json([
        'client_secret' => $intent->client_secret,
        'payment_id' => $payment->id,
    ]);
}


public function confirmStripePayment(Request $request)
{
    $request->validate([
        'payment_intent_id' => 'required|string',
        'payment_id' => 'required|integer'
    ]);

    $handler = new StripePaymentHandler();
    $intent = $handler->confirm($request->payment_intent_id);

    if ($intent->status === 'succeeded') {
        Payment::where('id', $request->payment_id)->update([
            'status' => 'paid',
            'payment_date' => now(),
            'transaction_id' => $intent->id,
        ]);
    }

    return response()->json($intent);
}

}
