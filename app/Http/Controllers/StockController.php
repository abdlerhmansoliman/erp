<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockStoreRequest;
use App\Http\Requests\StockUpdateRequest;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{

    public function __construct(protected StockService $stockService){}
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'sortBy', 'sortDir', 'perPage', 'page']);
        $stocks = $this->stockService->getAllStocks($filters);
        return response()->json($stocks);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StockStoreRequest $request)
    {
        $date=$request->validated();
        $stock = $this->stockService->create($date);
        return response()->json($stock, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stock = $this->stockService->findById($id);
        return response()->json($stock);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StockUpdateRequest $request, string $id)
    {
        $data=$request->validated();
        $stock = $this->stockService->findById($id);
        $updatedStock = $this->stockService->update($stock, $data);
        return response()->json($updatedStock);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
        $stock =$this->stockService->findById($id);
        if(!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
            
        }  
        $this->stockService->delete($stock);
        return response()->json(['message' => 'Stock deleted successfully'], 200);
     } catch (\Throwable $th) {
            throw $th;
        }

    }
}
