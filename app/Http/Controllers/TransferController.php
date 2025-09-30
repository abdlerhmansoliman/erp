<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferStoreRequest;
use App\Http\Resources\TransferResource;
use App\Services\TransferService;

class TransferController extends Controller
{
    protected $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function index()
    {
        $filters = request()->only(['search', 'sortBy', 'sortDirection', 'perPage']);
        $transfers = $this->transferService->getAllTransfers($filters);

        return TransferResource::collection($transfers);
    }
public function create()
{
    try {
        $fromWarehouseId = request()->query('from_warehouse_id');
        $data = $this->transferService->prepareTransferData(
            $fromWarehouseId ? (int)$fromWarehouseId : null
        );
        return response()->json($data);
    } catch (\Exception $e) {
        \Log::error('Transfer create error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to load transfer data',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

    public function store(TransferStoreRequest $request)
    {
        $transfer = $this->transferService->create($request->validated());

        return response()->json([
            'message'  => 'Transfer created successfully',
            'transfer' => $transfer
        ], 201);
    }

    public function show($id)
    {
        $transfer = $this->transferService->find($id);

        return response()->json($transfer);
    }
}
