<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
    ];

    public function stocks(){
        return $this->hasMany(Stock::class);
    }
    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }
    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }
    
}
