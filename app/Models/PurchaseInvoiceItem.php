<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    protected $table = 'purchase_invoice_items';
    protected $fillable = ['purchase_invoice_id', 'product_id', 'quantity', 'price', 'total','quantity_remaining'];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function PurchaseInvoice(){
        return $this->belongsTo(PurchaseInvoice::class);
    }
    
}
