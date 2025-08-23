<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $fillable =[
        'supplier_id',
        'status',
        'sub_total',
        'tax_amount',
        'grand_total',
        'discount_amount',
        'invoice_number'
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function items(){
        return $this->hasMany(PurchaseItems::class);
    }
    public function getInvoiceNumberAttribute()
{
    return 'INV-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
}
}
