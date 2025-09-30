<?php 

namespace App\Repositories;

use App\Models\Stock;
use App\Repositories\Interfaces\StockRepositoryInterface;

class StockRepository implements StockRepositoryInterface
{
    public function getAllStocks(array $filters)
    {
        return Stock::query()
            ->with(['product:id,name', 'warehouse:id,name'])
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function($query) use ($search) {
                    $query->where('id', 'like', "%$search%") 
                          ->orWhereHas('product', function($q2) use ($search) {
                              $q2->where('name', 'like', "%$search%");
                          })
                          ->orWhereHas('warehouse', function($q3) use ($search) {
                              $q3->where('name', 'like', "%$search%");
                          });
                });
            })
            ->orderBy($filters['sortBy'] ?? 'id', $filters['sortDirection'] ?? 'desc')
            ->paginate($filters['perPage'] ?? 10);
    }

    public function findById(int $id)
    {
        return Stock::find($id);
    }

    public function create(array $data)
    {
        return Stock::create($data);
    }

    public function update(Stock $stock, array $data)
    {
        $stock->update($data);
        return $stock;
    }

    public function delete(Stock $stock)
    {
        return $stock->delete();
    }

    public function getAvailableFIFO($product_id, $warehouse_id)
    {
        return Stock::where('product_id', $product_id)
                    ->where('warehouse_id', $warehouse_id)
                    ->where('remaining', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate()
                    ->get();
    }

    public function decrementRemaining($stockId, $quantity)
    {
        return Stock::where('id', $stockId)->decrement('remaining', $quantity);
    }

    public function getAvailableStock(int $productId, int $warehouseId): int
    {
        return Stock::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->sum('remaining');
    }

    /**
     * 🆕 Decrement remaining stock by product & warehouse (FIFO-based)
     */
    public function decrementRemainingByProduct(int $productId, int $warehouseId, float $quantity): void
    {
        $stocks = $this->getAvailableFIFO($productId, $warehouseId);
        $remaining = $quantity;

        foreach ($stocks as $stock) {
            if ($remaining <= 0) break;

            $takeQty = min($remaining, $stock->remaining);
            $stock->decrement('remaining', $takeQty);

            $remaining -= $takeQty;
        }

        if ($remaining > 0) {
            throw new \Exception("Not enough stock for product {$productId} in warehouse {$warehouseId}");
        }
    }

    /**
     * 🆕 Get last unit cost for product in warehouse
     */
    public function getLastCost(int $productId, int $warehouseId): float
    {
        return Stock::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->orderByDesc('created_at')
                    ->value('unit_coast') ?? 0;
    }
}
