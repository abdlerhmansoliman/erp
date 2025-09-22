<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $fillable=[
        'customer_id',
        'sub_total',
        'tax_amount',
        'grand_total',
        'discount_amount',
        'invoice_number',
        'warehouse_id',
        'status',
        'payment_status',
        'due_date',
        'shipping_cost',
        'payment_date'
    ];
    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (empty($model->invoice_number)) {
            $lastInvoice = self::latest('id')->first();
            $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;
            $model->invoice_number = 'SI-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }
    });
}
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(SalesItem::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

        public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

}
