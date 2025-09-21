<?php
// app/Payments/PaymentProcessor.php
namespace App\Payments;

use App\Models\Payment;

class PaymentProcessor
{
    protected array $strategies = [
        'stripe' => StripePaymentHandler::class,
        // ممكن تزود methods تانية زي cash/bank
    ];

    protected function resolveHandler(Payment $payment): PaymentStrategy
    {
        $methodName = strtolower($payment->paymentMethod->name ?? '');

        $handlerClass = $this->strategies[$methodName] ?? null;

        if (!$handlerClass) {
            throw new \Exception("Unsupported payment method: {$methodName}");
        }

        return app($handlerClass);
    }

    public function pay(Payment $payment): array
    {
        return $this->resolveHandler($payment)->pay($payment);
    }

    public function confirm(Payment $payment, array $data): array
    {
        return $this->resolveHandler($payment)->confirm($payment, $data);
    }

    public function webhook(string $methodName, array $payload): void
    {
        $handlerClass = $this->strategies[strtolower($methodName)] ?? null;
        if ($handlerClass) {
            app($handlerClass)->handleWebhook($payload);
        }
    }
}

