<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'description',
        'quantity',
        'price',
        'sku',
        'purchase_price',
        'sale_price',
        'category_id',
        'unit_id',
        
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function invoiceItems(){
        return $this->hasMany(InvoiceItem::class);         
    }
    public function unit(){
        return $this->belongsTo(Unit::class);
    }
    
}
