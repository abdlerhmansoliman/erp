<?php

namespace App\Services;

use App\Models\Stock;

use App\Repositories\StockRepository;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Create a new class instance.
     */
    protected $stockRepository;
    public function __construct(StockRepository $stockRepository)
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
    public function allocateFIFOStock($product_id, $warehouse_id, $quantity)
        {
            $allocations = [];

            DB::transaction(function () use ($product_id, $warehouse_id, $quantity, &$allocations) {
                $stocks = $this->stockRepository->getAvailableFIFO($product_id, $warehouse_id);
                $remaining = $quantity;

                foreach ($stocks as $stock) {
                    if ($remaining <= 0) break;
                    $takeQty = min($remaining, $stock->remaining);
                    $this->stockRepository->decrementRemaining($stock->id, $takeQty);

                    $allocations[] = [
                        'stock_id' => $stock->id,
                        'quantity' => $takeQty,
                        'cost'     => $stock->unit_coast
                    ];

                    $remaining -= $takeQty;
                }

                if ($remaining > 0) {
                    throw new \Exception("Out of stock: {$product_id}");
                }
            });

            return $allocations;
            
    }

}
