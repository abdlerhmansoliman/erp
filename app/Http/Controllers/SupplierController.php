<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Resources\SupplierResource;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function __construct(protected SupplierService $supplierService)
    {
        
    }
    public function index(Request $request)
    {
            $filters = $request->only(['search', 'sortBy', 'sortDir', 'perPage', 'page']);
        $suppliers = $this->supplierService->getAll($filters);

        return response()->json($suppliers);
    }
    public function show($id)
    {
        $supplier=$this->supplierService->getById($id);
        if(!$supplier){
            return response()->json(['message' => 'Supplier not found'], 404);
        }
        return new SupplierResource($supplier);
    }
    public function store(SupplierStoreRequest $request)
    {
        $data = $request->validated();
        $supplier = $this->supplierService->create($data);
        return new SupplierResource($supplier);
    }
    public function update(SupplierUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $supplier = $this->supplierService->getById($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
        $updatedSupplier = $this->supplierService->update($supplier, $data);
        return new SupplierResource($updatedSupplier);
    }
    public function destroy($id)
    {
        $supplier = $this->supplierService->getById($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
        $this->supplierService->delete($supplier);
        return response()->json(['message' => 'Supplier deleted successfully']);
    }
}
