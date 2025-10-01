<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable=[
        'from_warehouse_id',
        'to_warehouse_id',
        'transfer_date',
        'status',
        'created_by',
        'transfer_number'
    ];

    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->transfer_number)) {
                $lastTransfer = self::latest('id')->first();
                $nextNumber = $lastTransfer ? $lastTransfer->id + 1 : 1;
                $model->transfer_number = 'TR-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }
    public function items(){
        return $this->hasMany(TransferItem::class);
    }
    public function fromWarehouse(){
        return $this->belongsTo(Warehouse::class,'from_warehouse_id');
    }
    public function toWarehouse(){
        return $this->belongsTo(Warehouse::class,'to_warehouse_id');
    }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    
}
