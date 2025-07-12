<?php 

namespace App\Repositories;

use App\Models\Customer;
use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAllCustomers()
    {
        return User::all();
    }
    public function findById(int $id)
    {
        return Customer::findOrFail($id);
    }
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }
    public function delete(int $id): bool
    {
        $customer = Customer::findOrFail($id);
        return $customer->delete();
    }
    public function update(int $id, array $data)
    {
        $customer = Customer::findOrFail($id);
        $customer->update($data);
        return $customer;
    }

}