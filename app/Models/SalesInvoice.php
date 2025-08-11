<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $fillable=[
        'customer_id',
        'total_price',
        'notes',
        'invoice_date',

    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

}
