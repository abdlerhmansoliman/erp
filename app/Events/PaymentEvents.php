<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class PaymentEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Payment $payment)
    {
    }
}

class PaymentSucceeded extends PaymentEvent {}
class PaymentFailed extends PaymentEvent {}
class PaymentCancelled extends PaymentEvent {}