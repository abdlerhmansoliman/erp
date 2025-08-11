<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $fillable =[
        'supplier_id',
        'total_price',
        'notes',
        'invoice_date',
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function items(){
        return $this->hasMany(PurchaseInvoiceItem::class);
    }
}
