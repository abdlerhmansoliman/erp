<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable=[
        'purchase_returns_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function return(){
        return $this->belongsTo(PurchaseReturn::class);
    }
}
