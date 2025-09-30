<?php 

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProducetRepository implements ProductRepositoryInterface
{
        public function getAllProducts(array $filters)
        {
            return Product::with(['category', 'tax'])
                ->when($filters['search'] ?? null, function ($q, $search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhere('product_code', 'like', "%$search%");
                })
                ->orderBy($filters['sortBy'] ?? 'id', $filters['sortDirection'] ?? 'desc')
                ->paginate($filters['perPage'] ?? 10);
        }
    public function getProductById($id)
    {
        return Product::select('id', 'name', 'price', 'description','product_code', 'category_id', 'unit_id','purchase_price')->findOrFail($id);
    }

    public function createProduct(array $data)
    {
        return Product::create($data);
    }
    public function updateProduct($id, array $data)
    {
    $product=Product::findOrfail($id);
    return $product->update($data);
    }
    public function deleteProduct($id)
    {
        return Product::destroy($id);
    }

    public function deleteMultipleProducts(array $ids){
        return Product::whereIn('id',$ids)->delete();
    }
    public function search($query, $limit = 20)
    {
        return Product::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('product_code', 'like', "%{$query}%")
            ->with( 'taxes:id,name,rate')
            ->select('id', 'product_code', 'name', 'price','description','purchase_price','category_id','unit_id')
            ->limit($limit)
            ->get();
    }
// In your ProductRepository
public function getAvailableProductsByWarehouse(int $warehouseId)
{
    return Product::whereHas('stocks', function($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId)
                  ->where('qty', '>', 0);
        })
        ->with(['stocks' => function($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }])
        ->get()
        ->map(function($product) {
            $stock = $product->stocks->first();
            return [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'unit' => $product->unit,
                'available_qty' => $stock->qty ?? 0,
                'unit_cost' => $stock->unit_cost ?? $product->cost,
                'net_unit_price' => $product->price,
            ];
        });
}
}