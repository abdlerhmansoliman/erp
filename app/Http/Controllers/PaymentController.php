<?php

// app/Http/Controllers/PaymentController.php
namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service) {}

    public function pay(PaymentStoreRequest $request)
    {
        $data = $request->validated();

        return response()->json($this->service->createPayment($data));
    }

    public function confirm(Request $request, Payment $payment)
    {
        return response()->json(
            $this->service->confirmPayment($payment, $request->all())
        );
    }
}

