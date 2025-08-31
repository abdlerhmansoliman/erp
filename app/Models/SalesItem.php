<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    protected $table = 'sales_items';
    protected $fillable = [
    'sales_invoice_id',
    'product_id',
    'quantity',
    'unit_price',
    'tax_amount',
    'discount_amount',
    'total_price',
    'net_price',
    'tax_id',
    'warehouse_id',
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function salesInvoice(){
        return$this->belongsTo(SalesInvoice::class);
    }
}
