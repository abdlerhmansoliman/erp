<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(protected PaymentRepository $paymentRepo){}

    public function  addPayment(string $type, int $id, float $amount,string $dueDate,?string $paymentDate = null)
    {
    return  DB::transaction(function ()use ($type, $id,$amount ,$dueDate, $paymentDate) {
        return $this->paymentRepo->create([
            'payable_type' => $type,
            'payable_id' => $id,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'due_date' => $dueDate,
        ]);
    });
    }
    public function getTotalPaid(string $type, int $id): float
    {
        return $this->paymentRepo->getTotalPaid($type, $id);
    }

}