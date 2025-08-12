<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
