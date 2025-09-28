<?php

// app/Http/Controllers/PaymentController.php
namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service) {}


}

