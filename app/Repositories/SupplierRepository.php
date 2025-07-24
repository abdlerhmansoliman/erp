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
public function update($id, array $data){
    $supplier=Supplier::findOrfail($id);
    return $supplier->update($data);
}

public function delete(Supplier $supplier)
{
    $result = $supplier->delete();
    Log::info('Deleting supplier: ', ['id' => $supplier->id, 'result' => $result]);
    return $result;
}
}