<?php

namespace App\Services;

use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Repositories\DashbordRepository;

class DashbordService
{
    public function __construct(protected DashbordRepository $dashbordRepository){}
   
    public function getOverviewData()
    {
        return [
            'stats' => $this->dashbordRepository->getStats(),
            'last_7_days_sales' => $this->dashbordRepository->getLast7Days(SalesInvoice::class),
            'last_7_days_purchases' => $this->dashbordRepository->getLast7Days(PurchaseInvoice::class),
            'recent_sales' => $this->dashbordRepository->getRecntlySales(),
            'recent_purchases' => $this->dashbordRepository->getRecntlyPurchases(),
        ];
    }
}