<?php


namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Payments\TransactionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    public function handleStripe(Request $request)
    {
        $payload    = $request->getContent();
        $sigHeader  = $request->header('Stripe-Signature');
        $secret     = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $secret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed', [
                'error' => $e->getMessage(),
                'header' => $sigHeader
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Store webhook event for testing
        app(WebhookEventController::class)->store([
            'type' => $event->type,
            'data' => $event->data->object->toArray()
        ]);

        $handler = app(TransactionHandler::class);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $intent = $event->data->object;

                Log::info("✅ PaymentIntent succeeded", [
                    'id' => $intent->id,
                    'amount' => $intent->amount,
                    'currency' => $intent->currency,
                ]);

                if ($transaction = Transaction::where('transaction_id', $intent->id)->first()) {
                    $handler->handleSuccess($transaction, $intent->toArray());
                }
                break;

            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                
                Log::warning("❌ Payment failed", [
                    'id' => $intent->id,
                    'error' => $intent->last_payment_error ?? null
                ]);
                if ($transaction = Transaction::where('transaction_id', $intent->id)->first()) {
                    $handler->handleFailure($transaction, $intent->toArray());
                }
                break;

            case 'charge.succeeded':
                $charge = $event->data->object;

                Log::info("💳 Charge succeeded", [
                    'id' => $charge->id,
                    'payment_intent' => $charge->payment_intent,
                    'amount' => $charge->amount,
                ]);
                break;

            default:
                Log::info("Received webhook event: {$event->type}");
        }

        return response()->json(['status' => 'success']);
    }
}

