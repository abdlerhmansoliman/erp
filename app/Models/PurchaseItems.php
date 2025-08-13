<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItems extends Model
{
    protected $fillable = [
    'purchase_invoice_id',
    'product_id',
    'warehouse_id',
    'quantity',
    'unit_price',
    'total_price'
];


    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function PurchaseInvoice(){
        return $this->belongsTo(PurchaseInvoice::class);
    }
}
