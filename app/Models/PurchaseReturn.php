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
        'invoice_number',
        'note',
    ];
    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->invoice_number)) {
                $lastInvoice = self::latest('id')->first();
                $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;
                $model->invoice_number = 'PR-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }
    public function invoice(){
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
    public function items(){
        return $this->hasMany(PurchaseReturnItem::class,'purchase_returns_id');
    }
    
}
