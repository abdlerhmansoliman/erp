<?php 

namespace App\Repositories;

use App\Models\Warehouse;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class WarehouseRepository implements WarehouseRepositoryInterface
{

public function getAllWarehouses( array $filters)
{
return Warehouse::query()
    ->withStockSummary()
    ->when($filters['search'] ?? null, function($q, $search){
        $q->where('name','like',"%$search%")
          ->orWhere('address','like',"%$search%");
    })
    ->orderBy($filters['sortBy'] ?? 'id', $filters['sortDirection'] ?? 'desc')
    ->paginate($filters['perPage'] ?? 10);
}
public function getById($id)
{
    return Warehouse::withStockSummary()->find($id);
}
public function create(array $data)
{
    return Warehouse::create($data);
}
public function update(Warehouse $warehouse , array $data)
{
    $warehouse->update($data);
    return $warehouse;
}
public function delete(Warehouse $warehouse)
{
   $result = $warehouse->delete();
    return $result;
}
public function allWarehouses()
{
    return Warehouse::all();
}
public function getByIdWithAvailableProducts($id,$filters )
{
    $search = $filters['search'] ?? null;
    $sortBy = $filters['sortBy'] ?? 'product_name';
    $sortDirection = $filters['sortDirection'] ?? 'asc';
    $perPage = $filters['perPage'] ?? 10;

    $warehouse = Warehouse::withCount([
            'stocks as product_count' => function ($q) use ($search) {
                $q->select(DB::raw('COUNT(DISTINCT product_id)'))
                  ->where('remaining', '>', 0)
                  ->when($search, fn($query) => $query->whereHas('product', fn($q) => $q->where('name', 'like', "%{$search}%")));
            }
        ])
        ->withSum([
            'stocks as total_quantity' => function ($q) use ($search) {
                $q->where('remaining', '>', 0)
                  ->when($search, fn($query) => $query->whereHas('product', fn($q) => $q->where('name', 'like', "%{$search}%")));
            }
        ], 'remaining')
        ->findOrFail($id);

    $products = DB::table('stocks')
        ->join('products', 'stocks.product_id', '=', 'products.id')
        ->select(
            'stocks.product_id',
            'products.name as product_name',
            DB::raw('SUM(stocks.qty) as qty'),
            DB::raw('SUM(stocks.remaining) as remaining'),
            DB::raw('ROUND(AVG(stocks.unit_coast), 2) as unit_price'),
            DB::raw('ROUND(SUM(stocks.unit_coast * stocks.remaining), 2) as total_price')
        )
        ->where('stocks.warehouse_id', $id)
        ->where('stocks.remaining', '>', 0)
        ->when($search, fn($q) => $q->where('products.name','like',"%{$search}%"))
        ->groupBy('stocks.product_id','products.name')
        ->orderBy($sortBy, $sortDirection)
        ->paginate($perPage);

    // Add warehouse info to paginated response
    $products->getCollection()->transform(function ($item) {
        return $item;
    });

    // Add warehouse summary to meta
    $response = $products->toArray();
    $response['warehouse'] = [
        'id' => $warehouse->id,
        'name' => $warehouse->name,
        'product_count' => $warehouse->product_count,
        'total_quantity' => $warehouse->total_quantity
    ];

    return $response;
    }

}   