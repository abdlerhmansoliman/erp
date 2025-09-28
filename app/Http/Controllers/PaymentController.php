<?php

// app/Http/Controllers/PaymentController.php
namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Payment;
use App\Models\SalesInvoice;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service) {}
        public function getByInvoice(int $invoiceId): JsonResponse
        {
            $payment = Payment::where(function($query) use ($invoiceId) {
                $query->where('payable_type', 'App\\Models\\Sale')
                    ->orWhere('payable_type', 'App\\Models\\SaleInvoice') // Add this if your sale model is named SaleInvoice
                    ->orWhere('payable_type', get_class(new SalesInvoice())); // Dynamic class name
            })
            ->where('payable_id', $invoiceId)
            ->first();

            if (!$payment) {
                return response()->json([
                    'error' => 'Payment not found for this invoice'
                ], 404);
            }

            return response()->json($payment);
        }

}

