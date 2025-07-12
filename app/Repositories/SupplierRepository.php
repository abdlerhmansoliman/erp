<?php 

namespace App\Repositories;

use App\Models\Supplier;
use App\Models\User;
use App\Repositories\Interfaces\SupplierRepositoryInterface;

class SupplierRepository implements SupplierRepositoryInterface
{
public function All(){
    return Supplier::all();
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
public function delete($id){
    return Supplier::destroy($id);
}
}