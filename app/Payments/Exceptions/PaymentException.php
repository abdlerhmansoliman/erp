<?php

namespace App\Payments\Exceptions;

class PaymentException extends \Exception
{
    protected $payment;
    protected $errorCode;

    public function __construct(string $message, $payment = null, string $code = null)
    {
        parent::__construct($message);
        $this->payment = $payment;
        $this->errorCode = $code;
    }

    public function getPayment()
    {
        return $this->payment;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}

class PaymentProviderException extends PaymentException {}
class InvalidPaymentStatusException extends PaymentException {}
class PaymentAlreadyProcessedException extends PaymentException {}