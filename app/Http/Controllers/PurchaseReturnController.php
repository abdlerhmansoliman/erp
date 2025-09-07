<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReturnRequest;
use App\Http\Resources\PurchaseReturnResource;
use App\Models\PurchaseReturn;
use App\Services\PurchaseReturnsService;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected PurchaseReturnsService $returnService){}
public function index()
{
    try {
        $filters = request()->only(['search', 'sortBy', 'sortDir', 'perPage', 'page']);
        $returns = $this->returnService->getAllReturns($filters);
        return PurchaseReturnResource::collection($returns)
            ->response()
            ->setStatusCode(200);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */

public function store(PurchaseReturnRequest $request)
{
    $data = $request->validated();

    try {
        $purchaseReturn = $this->returnService->createReturn($data);

        return response()->json([
            'success' => true,
            'message' => 'Purchase return created successfully',
            'data' => $purchaseReturn
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
