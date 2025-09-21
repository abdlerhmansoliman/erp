<?php

// app/Http/Controllers/WebhookController.php
namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function __construct(protected WebhookService $service) {}

public function handleStripe(Request $request)
{
    $payload = $request->all();

    $event = $payload['type'] ?? null;

    if ($event === 'payment_intent.succeeded') {
        $intent = $payload['data']['object'];
        $payment = Payment::where('transaction_id', $intent['id'])->first();

        if ($payment) {
            $payment->status = 'succeeded';
            $payment->save();
        }
    }

    if ($event === 'payment_intent.payment_failed') {
        $intent = $payload['data']['object'];
        $payment = Payment::where('transaction_id', $intent['id'])->first();

        if ($payment) {
            $payment->status = 'failed';
            $payment->save();
        }
    }

    return response()->json(['status' => 'ok']);
}

}
