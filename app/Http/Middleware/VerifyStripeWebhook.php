<?php

namespace App\Http\Middleware;

use Closure;
use Stripe\WebhookSignature;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;

class VerifyStripeWebhook
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            $secret = config('services.stripe.webhook_secret');

            \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $secret
            );

            return $next($request);
        } catch (SignatureVerificationException $e) {
            abort(400, 'Invalid signature');
        } catch (\Exception $e) {
            abort(400, 'Invalid payload');
        }
    }
}