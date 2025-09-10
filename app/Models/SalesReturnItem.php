<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    protected $fillable=[
        'sales_return_id',
        'product_id',
        'tax_id',
        'quantity',
        'unit_price',
        'total_price',
        'tax_amount',
        'discount_amount',
        'net_price'
    ];

    public function salesReturn(){
        return $this->belongsTo(SalesReturn::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
    
}
