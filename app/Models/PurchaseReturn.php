<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable=[
        'purchase_invoice_id',
        'supplier_id',
        'warehouse_id',
        'status',
        'return_date',
        'sub_total',
        'tax_amount',
        'discount_amount',
        'grand_total',
    ];
    public function invoice(){
        return $this->belongsTo(PurchaseInvoice::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
    public function items(){
        return $this->hasMany(PurchaseReturnItem::class,'purchase_returns_id');
    }
    
}
