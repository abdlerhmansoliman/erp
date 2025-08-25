<?php 
  
namespace App\Repositories;

use App\Models\Supplier;
use App\Models\User;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SupplierRepository implements SupplierRepositoryInterface
{
public function All(array $filters){
    return Supplier::query()
    ->when($filters['search']??null,function($q,$search){
        $q->where('name','like',"%$search%")
        ->orWhere('email','like',"%$search%");
    })->orderBY($filters['sortBy']??'id',$filters['sortDirection']??'desc')
    ->paginate($filters['perPage']??10);
}
public function find($id){
    return Supplier::findOrFail($id);
}
public function create(array $data){
    return Supplier::create($data);
}
public function update(Supplier $supplier, array $data)
{
    $supplier->update($data);
    return $supplier;
}


public function delete(Supplier $supplier)
{
    $result = $supplier->delete();
    return $result;
}
public function deleteMultiple(array $ids): int
{
    return Supplier::whereIn('id', $ids)->delete();
}
public function allSuppliers(){
    return Supplier::all();
}
}