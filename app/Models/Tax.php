<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = ['name', 'rate','type','applies_to'];

    public function product()
    {
        return $this->belongsToMany(Product::class);
    }

    

}
