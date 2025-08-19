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
        public function increase(int $productId, int $warehouseId, int $qty): void
    {
        $stock = Stock::firstOrCreate(
            ['product_id' => $productId, 'warehouse_id' => $warehouseId],
            ['quantity' => 0]
        );
        $stock->increment('quantity', $qty);
    }

    public function decrease(int $productId, int $warehouseId, int $qty): void
    {
        $stock = Stock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        if ($stock) {
            $stock->decrement('quantity', $qty);
        }
    }
}
