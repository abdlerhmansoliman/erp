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
        'price',
        'sku',
        'purchase_price',
        'category_id',
        'unit_id',
        'product_code'
        
    ];
    protected static function booted()
    {
        static::creating(function ($product) {
            $latestId = self::max('id') + 1;
            $product->product_code = 'PRD-' . str_pad($latestId, 4, '0', STR_PAD_LEFT);
        });
    }
protected $with=['category','unit'];
    public function stocks() {
        return $this->hasMany(Stock::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
    public function purchaseInvoiceItems()
    {
        return $this->hasMany(PurchaseItems::class);
    }
    public function SalesItems()
    {
        return $this->hasMany(SalesItem::class);
    }
    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'product_tax');
    }
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'product_discount');
    }
}
