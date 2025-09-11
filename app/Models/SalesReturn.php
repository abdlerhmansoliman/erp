<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $fillable=[
        'sales_invoice_id',
        'customer_id',
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
                $model->invoice_number = 'SR-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
    public function items(){
        return $this->hasMany(SalesReturnItem::class,);
    }
    public function invoice() {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }
}
