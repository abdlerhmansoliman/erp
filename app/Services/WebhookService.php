<?php

// app/Services/WebhookService.php
namespace App\Services;

use App\Payments\PaymentProcessor;

class WebhookService
{
    public function __construct(protected PaymentProcessor $processor) {}

    public function handleStripe(array $payload): void
    {
        $this->processor->webhook('stripe', $payload);
    }
}
