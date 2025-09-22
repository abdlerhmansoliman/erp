<?php

namespace App\Payments\Exceptions;

use App\Models\Payment;
use Exception;

class PaymentProviderException extends Exception
{
    protected $payment;
    protected $providerCode;
    protected $providerResponse;

    public function __construct(string $message, Payment $payment, ?string $providerCode = null, $providerResponse = null)
    {
        parent::__construct($message, 0);
        $this->payment = $payment;
        $this->providerCode = $providerCode;
        $this->providerResponse = $providerResponse;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getProviderCode(): ?string
    {
        return $this->providerCode;
    }

    public function getProviderResponse()
    {
        return $this->providerResponse;
    }
}