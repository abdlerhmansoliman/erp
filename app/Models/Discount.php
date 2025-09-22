<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_percentage', 
        'value',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_discount');
    }   
}
