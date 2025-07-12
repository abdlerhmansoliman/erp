<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable=[
        'invoice_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'total_price',
        'invoiceable_type',
        'invoiceable_id',
    ];

    public function invoiceable(){
        return $this->morphTo();
    }
    public function product()
{
    return $this->belongsTo(\App\Models\Product::class);
}
}
