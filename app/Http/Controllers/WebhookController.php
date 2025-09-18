<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handle(Request $request){
        $secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        try {
            $event=Webhook::constructEvent(
                $payload, $sigHeader, $secret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        if($event->type=='payment_intent.succeeded'){
            $paymentIntent=$event->data->object;
            Payment::where('transaction_id', $paymentIntent->id)->update([
                'status' => 'succeeded',
                'payment_date' => now(),
            ]);
        }
                if ($event->type === 'payment_intent.payment_failed') {
            $paymentIntent = $event->data->object;

            Payment::where('transaction_id', $paymentIntent->id)
                ->update([
                    'status' => 'failed'
                ]);
        }

        return response('Webhook received', 200);
    
    }
}
