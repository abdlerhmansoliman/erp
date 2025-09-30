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
    ];

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
