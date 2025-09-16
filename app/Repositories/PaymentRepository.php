<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
public function create(array $data)
    {
        return Payment::create($data);
    }

    public function getByPayable(string $type, int $id)
    {
        return Payment::where('payable_type', $type)
            ->where('payable_id', $id)
            ->get();
    }

    public function getTotalPaid(string $type, int $id)
    {
        return Payment::where('payable_type', $type)
            ->where('payable_id', $id)
            ->sum('amount');
    }
}
