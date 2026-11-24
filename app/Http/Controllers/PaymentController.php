<?php

// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SalesInvoice;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service) {}

    public function getByInvoice(int $invoiceId): JsonResponse
    {
        $payment = Payment::where(function ($query) {
            $query->where('payable_type', 'App\\Models\\Sale')
                ->orWhere('payable_type', 'App\\Models\\SaleInvoice') // Add this if your sale model is named SaleInvoice
                ->orWhere('payable_type', get_class(new SalesInvoice)); // Dynamic class name
        })
            ->where('payable_id', $invoiceId)
            ->first();

        if (! $payment) {
            return response()->json([
                'error' => 'Payment not found for this invoice',
            ], 404);
        }

        return response()->json($payment);
    }
}
