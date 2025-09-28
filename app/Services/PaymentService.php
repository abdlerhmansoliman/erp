<?php
// app/Services/PaymentService.php
namespace App\Services;

use App\Models\Payment;
use App\Payments\PaymentProcessor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(protected PaymentProcessor $processor) {}

 public function addPayment(
        string $type,
        int $id,
        float $amount,
        string $dueDate,
        string $paymentDate,
        string $status = 'pending'
    ): Payment {
        return DB::transaction(function () use ($type, $id, $amount, $dueDate, $paymentDate, $status) {
            return Payment::create([
                'payable_type' => $type,
                'payable_id'   => $id,
                'amount'       => $amount,
                'due_date'     => $dueDate,
                'status'       => $status,
            ]);
        });
    }

}

