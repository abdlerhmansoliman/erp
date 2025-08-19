<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'type', // invoice or product
        'is_percentage', // true for percentage, false for fixed amount
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_discount');
    }   
}
