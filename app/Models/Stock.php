<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'product_unit_id',
        'warehouse_id',
        'model_type',
        'model_id',
        'qty',
        'remaining',
        'net_unit_price',
        'unit_coast'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Polymorphic relation: source of transaction (invoice, return, transfer...)
    public function model()
    {
        return $this->morphTo();
    }
    
}
