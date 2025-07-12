<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerSotreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $customerService)
    {
    }
    public function index (){
        $customers=$this->customerService->getAllCustomers();
        return CustomerResource::collection($customers);
    }
    public function show($id)
    {
        $customer=$this->customerService->findById($id);
        if(!$customer){
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return new CustomerResource($customer);
    }
    public function store(CustomerSotreRequest $request)
    {
        $data = $request->validated();
        $customer = $this->customerService->create($data);
        return new CustomerResource($customer);
    }
    public function update(CustomerUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $customer = $this->customerService->findById($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        $updatedCustomer = $this->customerService->update($id, $data);
        return new CustomerResource($updatedCustomer);
    }
    public function destroy($id)
    {
        $customer = $this->customerService->findById($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        $this->customerService->delete($id);
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}

