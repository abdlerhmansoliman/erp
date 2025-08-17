<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'purchase_items'; 
    protected $fillable = [
        'purchase_invoice_id',
        'product_id',
        'warehouse_id', 
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(\App\Models\PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}

