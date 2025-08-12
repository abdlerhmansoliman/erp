<?php 

namespace App\Repositories;

use App\Models\Warehouse;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
class WarehouseRepository implements WarehouseRepositoryInterface
{

public function getAllWarehouses( array $filters)
{
    return Warehouse::query()
    ->when($filters['search']??null , function($q,$search){
        $q->where('name','like',"%$search%")
        ->orWhere('address','like',"%$search%");
    })->orderBY($filters['sortBy']??'id',$filters['sortDirection']??'desc')
    ->paginate($filters['perPage']??10);
}
public function getById($id)
{
    return Warehouse::findOrFail($id);
     
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
}