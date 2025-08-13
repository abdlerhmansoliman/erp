<?php

namespace App\Services;

use App\Models\Stock;
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
    public function findById(int $id){
        return $this->stockRepository->findById($id);
    }
    public function create(array $data){
        return $this->stockRepository->create($data);
    }
    public function update(Stock $stock, array $data)
    {
        return $this->stockRepository->update($stock, $data);
    }
    public function delete(Stock $stock)
    {
        return $this->stockRepository->delete($stock);
    }
}
