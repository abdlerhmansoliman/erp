<?php

namespace App\Services;

use App\Repositories\PurchaseReturnRepository;

class PurchaseReturnsService
{
    public function __construct(protected PurchaseReturnRepository $purchaseReturnRepository)
    {}

    public function getAllReturns(array $filters)
    {
        return $this->purchaseReturnRepository->all($filters);
    }
}