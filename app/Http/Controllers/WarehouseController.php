<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseStoreRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Http\Resources\WarehouseResource;
use App\Http\Resources\WarehouseShowResource;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected WarehouseService $warehouseService) {}
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'sortBy', 'sortDir', 'perPage', 'page']);
        $warehouses = $this->warehouseService->getAllWarehouses($filters);
        return WarehouseResource::collection($warehouses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WarehouseStoreRequest $request)
    {
        $data=$request->validated();
        $warehouse=$this->warehouseService->create($data);
        return new WarehouseResource($warehouse);

    }

    /**
     * Display the specified resource.
     */
        public function show(string $id)
        {
            $filters = request()->only(['search', 'sortBy', 'sortDirection', 'perPage']);
            
            // Get basic warehouse info
            $warehouse = $this->warehouseService->getById($id);
            // Get products with pagination
            $productsData = $this->warehouseService->getByIdWithAvailableProducts($id, $filters);
            
            // Combine them
            $warehouse->products_data = $productsData;
            return new WarehouseShowResource($warehouse);  // ← Use the new resource
        }

    /**
     * Update the specified resource in storage.
     */
    public function edit(string $id)
    {
        $warehouse=$this->warehouseService->getById($id);
        return new WarehouseResource($warehouse);
    }
    public function update(WarehouseUpdateRequest $request,$id)
    {
        $data=$request->validated();
        $warehouse=$this->warehouseService->getById($id);
        if(!$warehouse){
            return response()->json(['message' => 'Warehouse not found'], 404);
        }
        $updatedWarehouse=$this->warehouseService->update($warehouse,$data);
        return new WarehouseResource($updatedWarehouse);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouse=$this->warehouseService->getById($id);
        $this->warehouseService->delete($warehouse);
        return response()->json(['message' => 'Warehouse deleted successfully']);
    }
}
