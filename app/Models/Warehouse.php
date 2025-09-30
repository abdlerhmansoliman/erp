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
    public function sales()
    {
        return $this->hasMany(SalesInvoice::class);
    }
    
    public function scopeWithStockSummary($query)
    {
        return $query
            ->withCount(['stocks as product_count' => function ($q) {
                $q->select(\DB::raw('COUNT(DISTINCT product_id)'));
            }])
            ->withSum('stocks as total_quantity', 'remaining');
    }
    
    public function transfersFrom()
    {
        return $this->hasMany(Transfer::class, 'from_warehouse_id');
    }
    public function transfersTo()
    {
        return $this->hasMany(Transfer::class, 'to_warehouse_id');
    }
}
