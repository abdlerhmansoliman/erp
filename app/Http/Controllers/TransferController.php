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
