<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    protected $table = 'sales_items';
    protected $fillable = [
        'sales_invoice_id', 'product_id', 'unit_sale_price', 'unit_cost_price', 'total_sale_price', 'total_cost_price', 'total_price'
    ];
    public function product(){
        $this->belongsTo(Product::class);
    }
    public function salesInvoice(){
        $this->belongsTo(SalesInvoice::class);
    }
}
