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
        'invoice_number',
        'warehouse_id',
    ];
protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (empty($model->invoice_number)) {
            $lastInvoice = self::latest('id')->first();
            $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;
            $model->invoice_number = 'PO-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }
    });
}
    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function items(){
        return $this->hasMany(PurchaseItems::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function returns(){
        return $this->hasMany(PurchaseReturn::class);
    }
    

}
