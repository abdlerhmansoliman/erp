<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = ['name', 'rate'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItems::class);
    }

    

}
