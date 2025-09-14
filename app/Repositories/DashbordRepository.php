<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\Supplier;
use App\Repositories\Interfaces\DashbordRepositoryInterface;

class DashbordRepository implements DashbordRepositoryInterface
{
    public function getStats()
    {
        return[
        'total_sales' => SalesInvoice::whereDate('created_at',today())->sum('grand_total'),
        'total_purchases' => PurchaseInvoice::whereDate('created_at',today())->sum('grand_total'),
        'total_customers' => Customer::count(),
        'total_suppliers' => Supplier::count(),
        'total_products' => Product::count(),
        ];
    }
    public function getLast7Days(string $model)
    {
        return $model::selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    public function getRecntlySales()
    {
        return SalesInvoice::latest()->take(5)->get(['id','invoice_number','grand_total','created_at']);
    }
    public function getRecntlyPurchases()
    {
        return PurchaseInvoice::latest()->take(5)->get(['id','invoice_number','grand_total','created_at']);
    }


}
