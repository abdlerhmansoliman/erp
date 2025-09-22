<?php


namespace App\Http\Controllers;

use App\Models\Payment;
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
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 🔹 فلترة حسب نوع الحدث
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $intent = $event->data->object;

                Log::info("✅ PaymentIntent succeeded", [
                    'id' => $intent->id,
                    'amount' => $intent->amount,
                    'currency' => $intent->currency,
                ]);

                // مثال: تحديث حالة الدفع في DB
                Payment::where('stripe_id', $intent->id)
                    ->update(['status' => 'succeeded']);
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
                Log::warning("Unhandled event type: {$event->type}");
        }

        return response()->json(['status' => 'success']);
    }
}

