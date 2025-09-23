<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'payment_id',
        'payment_method_id',
        'provider',
        'transaction_id',
        'amount',
        'status',
        'provider_response',
        'paid_at',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
