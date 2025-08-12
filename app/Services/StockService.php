<?php

namespace App\Services;

use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\StockRepositoryInterface;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Hash;

class StockService
{
    /**
     * Create a new class instance.
     */
    protected $stockRepository;
    public function __construct(StockRepositoryInterface $stockRepository)
    {
        $this->stockRepository=$stockRepository;
    }
    
    public function getAllStocks(array $filters){
        return $this->stockRepository->getAllStocks($filters);
    }
}
